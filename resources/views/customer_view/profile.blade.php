@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <main class="main-content">
        <div class="profile-wrapper">
            <h2 class="section-header">// SYSTEM_OPERATOR_DATA</h2>
            <div class="profile-panel">
                <div class="avatar-section">
                    <input type="file" id="avatarUpload" accept="image/*" style="display: none;">
                    <div class="avatar-box" id="avatarTrigger" title="Upload new avatar">[ O ]</div>
                    <div class="status-badge">NETWORK: SECURE</div>
                    <p style="color: #6a8299; font-size: 14px; margin-top: 10px;">ID: OP-7729-X</p>
                </div>

                <div class="form-section">
                    @if(session('success'))
                        <div style="color: #00f2ff; border: 1px solid #00f2ff; padding: 15px; margin-bottom: 20px; background: rgba(0, 242, 255, 0.1);">
                            > {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div style="color: #ff003c; border: 1px solid #ff003c; padding: 15px; margin-bottom: 20px; background: rgba(255, 0, 60, 0.1);">
                            > ERROR: {{ $errors->first() }}
                        </div>
                    @endif

                    <form id="profileForm" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>COMM-LINK EMAIL (UNALTERABLE)</label>
                            <input type="email" value="{{ Session::get('user_email') }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>OPERATOR ALIAS (NAME)</label>
                            <input type="text" name="name" value="{{ Session::get('user_name') }}" required>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 10px;">
                            <div class="form-group">
                                <label>NEW SECURITY KEY</label>
                                <input type="password" name="password" placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label>VERIFY SECURITY KEY</label>
                                <input type="password" name="password_confirmation" placeholder="••••••••">
                            </div>
                        </div>
                        <div class="action-row">
                            <button type="submit" class="btn btn-primary">SAVE OVERRIDES</button>
                            <button type="reset" class="btn btn-outline">REVERT</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="danger-zone">
                <div class="danger-text">
                    <h3>// CRITICAL PROTOCOL: PURGE ACCOUNT</h3>
                    <p>Warning: Executing this command will permanently erase all operator data and order history. This action cannot be reversed.</p>
                </div>
                <button class="btn btn-danger" id="btnPurge">INITIATE PURGE</button>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        const avatarTrigger = document.getElementById('avatarTrigger');
        const avatarUpload = document.getElementById('avatarUpload');

        avatarTrigger.addEventListener('click', () => { avatarUpload.click(); });

        avatarUpload.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                avatarTrigger.style.background = 'rgba(255, 0, 255, 0.2)';
                avatarTrigger.style.borderColor = '#ff00ff';
                avatarTrigger.innerHTML = '[ OK ]';
            }
        });

        document.getElementById('btnPurge').addEventListener('click', function() {
            const confirmation = confirm("// WARNING // \nAre you absolutely certain you wish to purge this profile? This will sever your Comm-Link permanently.");
            if(confirmation) {
                document.body.innerHTML = '<h1 style="color:#ff003c; text-align:center; margin-top:20vh; font-family:Courier New;">CONNECTION TERMINATED.</h1>';
            }
        });
    </script>
@endsection