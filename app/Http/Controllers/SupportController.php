<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use App\Models\SupportMessage;
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
            $requests = SupportRequest::where('user_id', Auth::id())
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
            $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
                'captcha' => 'required|string'
            ]);

            // Validate CAPTCHA
            $captchaCode = session('captcha_code');
            if (!$captchaCode || !hash_equals(strtoupper($captchaCode), strtoupper($request->captcha))) {
                return back()->withErrors([
                    'captcha' => 'Invalid CAPTCHA code.',
                ])->withInput();
            }

            // Clear CAPTCHA from session
            session()->forget('captcha_code');

            $supportRequest = SupportRequest::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'status' => 'open'
            ]);

            SupportMessage::create([
                'support_request_id' => $supportRequest->id,
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

            // Check if ticket is closed
            if ($supportRequest->status === 'closed') {
                return redirect()->route('support.show', $supportRequest->ticket_id)
                    ->with('error', 'Cannot reply to a closed support request. If you need further assistance, please create a new support request.');
            }

            $request->validate([
                'message' => 'required|string|max:5000',
                'captcha' => 'required|string'
            ]);

            // Validate CAPTCHA
            $captchaCode = session('captcha_code');
            if (!$captchaCode || !hash_equals(strtoupper($captchaCode), strtoupper($request->captcha))) {
                return back()->withErrors([
                    'captcha' => 'Invalid CAPTCHA code.',
                ])->withInput();
            }

            // Clear CAPTCHA from session
            session()->forget('captcha_code');

            SupportMessage::create([
                'support_request_id' => $supportRequest->id,
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
