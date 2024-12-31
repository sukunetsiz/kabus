<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RhombusCaptchaController extends Controller
{
    private const GRID_SIZE = 8;
    private const MIN_COORDINATE = 1;
    private const MAX_COORDINATE = 8;
    private const RANDOM_LETTER_CHANCE = 30;

    private const STYLE_RANGES = [
        'hue' => [0, 360],
        'saturation' => [70, 100],
        'lightness' => [60, 80],
        'hopDuration' => [1.5, 2.5],
        'hopDelay' => [0, 2.0],
        'rotation' => [-10, 10]
    ];

    public function show(): RedirectResponse|View
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $viewData = $this->prepareViewData();
        
        // If there's a flash message from a previous verification attempt, add it to the view data
        if (Session::has('message')) {
            $viewData['message'] = Session::get('message');
        }

        return view('rhombus-captcha', $viewData);
    }

    public function verify(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        if (!preg_match('/^[1-8]-[1-8]$/', $request->input('captcha_answer'))) {
            // Flash the error message and redirect to root
            Session::flash('message', 'Please read the instructions carefully and enter the coordinates in the correct format.');
            return redirect()->to('/');
        }

        $userAnswer = $this->formatCoordinateAnswer($request->input('captcha_answer'));
        $correctAnswer = Session::get('captcha_answer');

        if ($userAnswer === $correctAnswer) {
            Session::put('captcha_solved', true);
            return redirect()->route('login');
        }

        // Flash the error message and redirect to root
        Session::flash('message', 'Incorrect answer. Please try again.');
        return redirect()->to('/');
    }

    private function prepareViewData(): array
    {
        $captchaGrid = $this->generateCaptcha();
        $gameLetter = Session::get('game_letter');
        $exampleLetter = $this->getRandomExampleLetter($captchaGrid, $gameLetter);

        return [
            'captchaGrid' => $captchaGrid,
            'gameLetter' => $gameLetter,
            'exampleLetter' => $exampleLetter,
            'gridSize' => self::GRID_SIZE,
            'minCoordinate' => self::MIN_COORDINATE,
            'maxCoordinate' => self::MAX_COORDINATE,
            'gameInstructions' => [
                'shortInstruction' => "Find the appropriate box for the letter '{$gameLetter}'"
            ],
            'coordinateExample' => $this->getCoordinateExample($exampleLetter)
        ];
    }

    private function getCoordinateExample(?array $exampleLetter): ?array
    {
        if (!$exampleLetter) {
            return null;
        }

        return [
            'letter' => $exampleLetter['letter'],
            'coordinates' => [
                'x' => $exampleLetter['x'],
                'y' => $exampleLetter['y']
            ],
            'explanation' => "It is at coordinates {$exampleLetter['x']}-{$exampleLetter['y']}. This represents column {$exampleLetter['x']} and row {$exampleLetter['y']}. You can understand this by looking at the horizontal and vertical axes of the glowing letter."
        ];
    }

    private function formatCoordinateAnswer(string $answer): string
    {
        // Normalize input format (remove spaces, ensure single hyphen)
        return preg_replace('/\s+/', '', $answer);
    }

    private function generateCaptcha(): array
    {
        $letters = range('A', 'Z');
        
        // Initialize grid with empty cells and styles
        $grid = [];
        for ($y = 0; $y < self::GRID_SIZE; $y++) {
            $grid[$y] = [];
            for ($x = 0; $x < self::GRID_SIZE; $x++) {
                $grid[$y][$x] = [
                    'letter' => '',
                    'styles' => $this->generateLetterStyles()
                ];
            }
        }

        $centerX = rand(1, 6);
        $centerY = rand(1, 6);

        $gameLetter = $letters[array_rand($letters)];

        $edges = [
            [$centerY-1, $centerX],
            [$centerY+1, $centerX],
            [$centerY, $centerX-1],
            [$centerY, $centerX+1]
        ];

        // Place edge letters
        foreach ($edges as $edge) {
            if ($this->isValidCoordinate($edge[0], $edge[1])) {
                $grid[$edge[0]][$edge[1]]['letter'] = $gameLetter;
            }
        }

        // Handle center letter
        if (rand(0, 1) == 0) {
            $grid[$centerY][$centerX]['letter'] = '';
        } else {
            do {
                $centerLetter = $letters[array_rand($letters)];
            } while ($centerLetter == $gameLetter);
            $grid[$centerY][$centerX]['letter'] = $centerLetter;
        }

        // Create missing edge for the puzzle
        $validEdges = array_filter($edges, fn($edge) => $this->isValidCoordinate($edge[0], $edge[1]));
        $missingEdgeIndex = array_rand($validEdges);
        $missingEdge = $validEdges[$missingEdgeIndex];
        $grid[$missingEdge[0]][$missingEdge[1]]['letter'] = '';

        // Add random letters
        $this->addRandomLetters($grid, $letters, $centerX, $centerY, $edges);

        Session::put('captcha_answer', ($missingEdge[1] + 1) . '-' . ($missingEdge[0] + 1));
        Session::put('captcha_grid', $grid);
        Session::put('game_letter', $gameLetter);

        return $grid;
    }

    private function addRandomLetters(array &$grid, array $letters, int $centerX, int $centerY, array $edges): void
    {
        for ($y = 0; $y < self::GRID_SIZE; $y++) {
            for ($x = 0; $x < self::GRID_SIZE; $x++) {
                if (($y == $centerY && $x == $centerX) || in_array([$y, $x], $edges)) {
                    continue;
                }
                if ($grid[$y][$x]['letter'] === '' && rand(1, 100) <= self::RANDOM_LETTER_CHANCE) {
                    $grid[$y][$x]['letter'] = $letters[array_rand($letters)];
                }
            }
        }
    }

    private function generateLetterStyles(): array
    {
        $styles = [];
        foreach (self::STYLE_RANGES as $property => $range) {
            $styles[$property] = $this->generateRandomInRange($range[0], $range[1]);
        }
        return $styles;
    }

    private function generateRandomInRange(float $min, float $max, int $precision = 1): float
    {
        return round($min + (mt_rand() / mt_getrandmax()) * ($max - $min), $precision);
    }

    private function isValidCoordinate(int $y, int $x): bool
    {
        return $y >= 0 && $y < self::GRID_SIZE && $x >= 0 && $x < self::GRID_SIZE;
    }

    private function getRandomExampleLetter(array $grid, string $gameLetter): ?array
    {
        $examples = [];
        for ($y = 0; $y < self::GRID_SIZE; $y++) {
            for ($x = 0; $x < self::GRID_SIZE; $x++) {
                if ($grid[$y][$x]['letter'] !== '' && $grid[$y][$x]['letter'] !== $gameLetter) {
                    $examples[] = [
                        'letter' => $grid[$y][$x]['letter'],
                        'x' => $x + 1,
                        'y' => $y + 1
                    ];
                }
            }
        }
        return $examples ? $examples[array_rand($examples)] : null;
    }
}