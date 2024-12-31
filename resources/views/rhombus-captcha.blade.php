<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabus Market</title>
    <link rel="stylesheet" href="{{ asset('css/rhombus-captcha.css') }}">
</head>
<body>
    <div class="page-container">
        <!-- Left Examples -->
        <div class="side-examples left-examples">
            <div class="example-case">
                <span class="example-title">Top Edge Missing</span>
                <div class="mini-grid">
                    <div class="mini-cell"></div>
                    <div class="mini-cell missing">?</div>
                    <div class="mini-cell"></div>
                    <div class="mini-cell letter">O</div>
                    <div class="mini-cell empty"></div>
                    <div class="mini-cell letter">O</div>
                    <div class="mini-cell"></div>
                    <div class="mini-cell letter">O</div>
                    <div class="mini-cell"></div>
                </div>
                <span class="example-explanation">Coordinate: 2-1</span>
            </div>
            
            <div class="example-case">
                <span class="example-title">Right Edge Missing</span>
                <div class="mini-grid">
                    <div class="mini-cell"></div>
                    <div class="mini-cell letter">M</div>
                    <div class="mini-cell"></div>
                    <div class="mini-cell letter">M</div>
                    <div class="mini-cell empty"></div>
                    <div class="mini-cell missing">?</div>
                    <div class="mini-cell"></div>
                    <div class="mini-cell letter">M</div>
                    <div class="mini-cell"></div>
                </div>
                <span class="example-explanation">Coordinate: 3-2</span>
            </div>
        </div>

        <!-- Main Container -->
        <div class="rhombus-captcha-container">
            <h2>Rhombus CAPTCHA Puzzle</h2>
            <p>Find the missing edge to complete the rhombus made of '{{ $gameLetter }}' letters and determine the box where the 4th letter will be placed. Write the coordinate of this box in "x-y" format. "x" represents the horizontal coordinate, and "y" represents the vertical coordinate. You can use numbers from 1 to 8 for each coordinate.</p>
            
            <div class="rhombus-captcha-game-instruction">
                {{ $gameInstructions['shortInstruction'] }}
            </div>
            <div class="rhombus-captcha-caution">
                <strong>Example:</strong>
                @if ($coordinateExample)
                    In the table, the letter '{{ $coordinateExample['letter'] }}' {{ $coordinateExample['explanation'] }}
                @else
                    Sorry, example cannot be displayed at the moment.
                @endif
            </div>
            <table>
                <tr>
                    <th></th>
                    @for ($x = $minCoordinate; $x <= $maxCoordinate; $x++)
                        <th>{{ $x }}</th>
                    @endfor
                </tr>
                @for ($y = 0; $y < $gridSize; $y++)
                    <tr>
                        <th>{{ $y + 1 }}</th>
                        @for ($x = 0; $x < $gridSize; $x++)
                            <td @if ($coordinateExample && $x == $coordinateExample['coordinates']['x'] - 1 && $y == $coordinateExample['coordinates']['y'] - 1) class="rhombus-captcha-example-letter" @endif>
                                @if ($captchaGrid[$y][$x]['letter'] !== '')
                                    <span class="rhombus-captcha-letter" style="
                                        --rotation: {{ $captchaGrid[$y][$x]['styles']['rotation'] }}deg;
                                        --letter-color: hsl({{ $captchaGrid[$y][$x]['styles']['hue'] }}, {{ $captchaGrid[$y][$x]['styles']['saturation'] }}%, {{ $captchaGrid[$y][$x]['styles']['lightness'] }}%);
                                        --hop-duration: {{ $captchaGrid[$y][$x]['styles']['hopDuration'] }}s;
                                        --hop-delay: {{ $captchaGrid[$y][$x]['styles']['hopDelay'] }}s;
                                    ">
                                        {{ $captchaGrid[$y][$x]['letter'] }}
                                    </span>
                                    <div class="rhombus-captcha-overlay"></div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endfor
            </table>
            <form method="post" action="{{ route('rhombus-captcha.verify') }}">
                @csrf
                <input type="text" name="captcha_answer" placeholder="Example {{ $coordinateExample ? "{$coordinateExample['coordinates']['x']}-{$coordinateExample['coordinates']['y']}" : '4-6' }} (x,y)" required>
                <input type="submit" value="Submit">
            </form>
            @if (isset($message))
                <p class="rhombus-captcha-message">{{ $message }}</p>
            @endif
        </div>

        <!-- Right Examples -->
        <div class="side-examples right-examples">
            <div class="example-case">
                <span class="example-title">Bottom Edge Missing</span>
                <div class="mini-grid">
                    <div class="mini-cell"></div>
                    <div class="mini-cell letter">E</div>
                    <div class="mini-cell"></div>
                    <div class="mini-cell letter">E</div>
                    <div class="mini-cell empty"></div>
                    <div class="mini-cell letter">E</div>
                    <div class="mini-cell"></div>
                    <div class="mini-cell missing">?</div>
                    <div class="mini-cell"></div>
                </div>
                <span class="example-explanation">Coordinate: 2-3</span>
            </div>
            
            <div class="example-case">
                <span class="example-title">Left Edge Missing</span>
                <div class="mini-grid">
                    <div class="mini-cell"></div>
                    <div class="mini-cell letter">T</div>
                    <div class="mini-cell"></div>
                    <div class="mini-cell missing">?</div>
                    <div class="mini-cell empty"></div>
                    <div class="mini-cell letter">T</div>
                    <div class="mini-cell"></div>
                    <div class="mini-cell letter">T</div>
                    <div class="mini-cell"></div>
                </div>
                <span class="example-explanation">Coordinate: 1-2</span>
            </div>
        </div>
    </div>

    <!-- Rotating Rhombuses -->
    <div class="rhombus-container rhombus-left">
        <div class="rhombus"></div>
    </div>
    <div class="rhombus-container rhombus-right">
        <div class="rhombus"></div>
    </div>
</body>
</html>
