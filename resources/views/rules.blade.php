@extends('layouts.app')

@section('content')

<div class="rules-container">
    <div style="text-align: center;">
        <h1 class="rules-title">Marketplace Rules & Guidelines</h1>
    </div>
    @if(request()->get('page', 1) == 1)
        <div class="rules-section">
            <p>Welcome to our secure Monero marketplace. These rules are designed to ensure a safe, private, and efficient trading environment for all users. Compliance with these rules is mandatory for all marketplace participants.</p>

            <h2>Important Notice</h2>
            <p>The marketplace reserves the right to modify these rules as needed to maintain security and improve user experience. Users are responsible for staying updated with current rules. Violations may result in account suspension or permanent ban. Your security and privacy are our top priorities.</p>
        </div>
    @elseif(request()->get('page') == 2)
        <div class="rules-section">
            <div class="rules-item">
                <h3>Rule 1: Transaction Security</h3>
                <p>All transactions must use the platform's built-in escrow system. Direct trades or external escrow services are strictly prohibited to ensure user safety and prevent fraud. The platform escrow system is the only authorized method for conducting transactions.</p>
            </div>

            <div class="rules-item">
                <h3>Rule 2: Privacy Protection</h3>
                <p>Users must maintain strict privacy standards. Sharing personal information, attempting to deanonymize users (doxxing), or requesting personal details is strictly prohibited. Protect your privacy by using strong encryption and secure communication methods.</p>
            </div>

            <div class="rules-item">
                <h3>Rule 3: Secure Communication</h3>
                <p>All communication must occur through the platform's encrypted messaging system. External communication methods are prohibited to maintain user privacy and transaction security. Never share contact information or communicate through external channels.</p>
            </div>
        </div>
    @elseif(request()->get('page') == 3)
        <div class="rules-section">
            <div class="rules-item">
                <h3>Rule 4: Monero Transactions</h3>
                <p>All payments must be made exclusively in Monero (XMR). Ensure your wallet is properly secured and maintain good operational security practices. Double-check all transaction details before confirming any transfers.</p>
            </div>

            <div class="rules-item">
                <h3>Rule 5: Listing Standards</h3>
                <p>All listings must be clear, accurate, and compliant with international regulations. Misrepresenting products or services is prohibited. Prices must be clearly displayed in XMR, and all terms of sale must be explicitly stated.</p>
            </div>

            <div class="rules-item">
                <h3>Rule 6: Account Security</h3>
                <p>Users are responsible for maintaining their account security. Enable 2FA, use strong passwords, and never share account credentials. Report any suspicious activity immediately to marketplace administration.</p>
            </div>
        </div>
    @elseif(request()->get('page') == 4)
        <div class="rules-section">
            <div class="rules-item">
                <h3>Rule 7: Feedback System</h3>
                <p>Provide honest and accurate feedback after transactions. Manipulation of the feedback system, including fake reviews or rating extortion, is prohibited. Feedback should be based solely on the transaction experience.</p>
            </div>

            <div class="rules-item">
                <h3>Rule 8: Market Conduct</h3>
                <p>Maintain professional conduct in all marketplace interactions. Harassment, threats, or any form of abusive behavior will not be tolerated. Respect other users and resolve disputes through official channels.</p>
            </div>

            <div class="rules-item">
                <h3>Rule 9: Platform Security</h3>
                <p>Any attempt to compromise platform security, including hacking attempts or exploitation of vulnerabilities, is strictly prohibited. Users who discover security issues should report them to administration through secure channels.</p>
            </div>
        </div>
    @elseif(request()->get('page') == 5)
        <div class="rules-section">
            <h2>Dispute Resolution & Service Terms</h2>

            <ol>
                <li>All marketplace rules apply to dispute resolution processes.</li>
                <li>Disputes must be opened within 24 hours of transaction completion.</li>
                <li>Both parties must respond to dispute mediator requests within 48 hours.</li>
                <li>All evidence must be submitted through the platform's secure system.</li>
                <li>Moderator decisions are final but may be appealed through official channels.</li>
                <li>Users must maintain respectful communication during dispute resolution.</li>
            </ol>

            <div class="rules-note">
                <strong>Note:</strong> The marketplace administration maintains final authority in all dispute resolutions. While we strive for fairness and transparency, our primary goal is maintaining a secure and trustworthy trading environment. Users who believe a decision requires review may submit an appeal through official support channels.
            </div>
        </div>
    @endif

    <div class="pagination-container">
        {{ $paginatedRules->links('components.pagination') }}
    </div>
</div>
@endsection

