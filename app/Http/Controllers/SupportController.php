<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CaptchaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class SupportController extends Controller
{
    protected $captchaService;

    public function __construct(CaptchaService $captchaService)
    {
        $this->captchaService = $captchaService;
    }

    public function index()
    {
        try {
            $requests = SupportRequest::mainRequests()
                ->where('user_id', Auth::id())
                ->with('latestMessage')
                ->orderBy('created_at', 'desc')
                ->paginate(4);

            return view('support.index', compact('requests'));
        } catch (QueryException $e) {
            return redirect()->route('home')
                ->with('error', 'Support requests cannot be loaded. Please try again later.');
        }
    }

    public function create()
    {
        $captchaCode = $this->captchaService->generateCode();
        session(['captcha_code' => $captchaCode]);
        return view('support.create', ['captchaCode' => $captchaCode]);
    }

    public function store(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'title' => 'required|string|min:8|max:160',
                'message' => 'required|string|min:8|max:4000',
                'captcha' => 'required|string|min:2|max:8'
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
                $errorMessage = $errors->first();
                return back()->with('error', $errorMessage)->withInput();
            }

            // Validate CAPTCHA
            $captchaCode = session('captcha_code');
            if (!$captchaCode || !hash_equals(strtoupper($captchaCode), strtoupper($request->captcha))) {
                return back()
                    ->with('error', 'Invalid CAPTCHA code.')
                    ->withInput();
            }

            // Clear CAPTCHA from session
            session()->forget('captcha_code');

            // Create the main support request
            $supportRequest = SupportRequest::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'status' => 'open'
            ]);

            // Create the initial message
            $supportRequest->messages()->create([
                'user_id' => Auth::id(),
                'message' => $request->message,
                'is_admin_reply' => false
            ]);

            // Log new ticket creation
            Log::info("New support ticket created: {$supportRequest->ticket_id} by user {$supportRequest->user_id}");

            return redirect()->route('support.show', $supportRequest->ticket_id)
                ->with('success', 'Support request successfully created.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Support request could not be created. Please try again later.')
                ->withInput();
        }
    }

    public function show(SupportRequest $supportRequest)
    {
        try {
            $this->authorize('view', $supportRequest);

            // Only load messages if this is a main request
            if (!$supportRequest->isMainRequest()) {
                return redirect()->route('support.index')
                    ->with('error', 'Invalid support request.');
            }

            $messages = $supportRequest->messages()->with('user')->get();
            $captchaCode = $this->captchaService->generateCode();
            session(['captcha_code' => $captchaCode]);
            
            return view('support.show', compact('supportRequest', 'messages', 'captchaCode'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('support.index')
                ->with('error', 'You do not have permission to view this support request.');
        } catch (\Exception $e) {
            return redirect()->route('support.index')
                ->with('error', 'Support request could not be loaded. Please try again later.');
        }
    }

    public function reply(Request $request, SupportRequest $supportRequest)
    {
        try {
            $this->authorize('reply', $supportRequest);

            // Ensure we're replying to a main request
            if (!$supportRequest->isMainRequest()) {
                return redirect()->route('support.index')
                    ->with('error', 'Invalid support request.');
            }

            // Check if ticket is closed
            if ($supportRequest->status === 'closed') {
                return redirect()->route('support.show', $supportRequest->ticket_id)
                    ->with('error', 'Cannot reply to a closed support request. If you need further assistance, please create a new support request.');
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'message' => 'required|string|min:8|max:4000',
                'captcha' => 'required|string|min:2|max:8'
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
                $errorMessage = $errors->first();
                return back()->with('error', $errorMessage)->withInput();
            }

            // Validate CAPTCHA
            $captchaCode = session('captcha_code');
            if (!$captchaCode || !hash_equals(strtoupper($captchaCode), strtoupper($request->captcha))) {
                return back()
                    ->with('error', 'Invalid CAPTCHA code.')
                    ->withInput();
            }

            // Clear CAPTCHA from session
            session()->forget('captcha_code');

            // Create the reply message
            $supportRequest->messages()->create([
                'user_id' => Auth::id(),
                'message' => $request->message,
                'is_admin_reply' => false
            ]);

            return redirect()->route('support.show', $supportRequest->ticket_id)
                ->with('success', 'Reply sent successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('support.index')
                ->with('error', 'You do not have permission to reply to this support request.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Reply could not be sent. Please try again later.')
                ->withInput();
        }
    }
}