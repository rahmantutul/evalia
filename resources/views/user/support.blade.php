@extends('user.layouts.app')

@push('styles')
<style>
    .support-container {
        min-height: 100vh;
        background-color: #f8f9fa;
        padding: 2rem 0;
    }
    
    .support-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: none;
    }
    
    .support-header {
    background: linear-gradient(135deg, #f8fbff 0%, #e8f3ff 100%);
    padding: 2.5rem 2rem;
    color: #2c3e50;
    text-align: center;
    position: relative;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 4px 20px rgba(37, 117, 252, 0.08);
    }

    .support-header::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6a11cb 0%, #2575fc 50%, #2af598 100%);
    }

    .support-title {
        font-weight: 700;
        margin-bottom: 0.75rem;
        font-size: 1.8rem;
        position: relative;
        display: inline-block;
    }

    .support-icon {
        font-size: 2rem;
        margin-bottom: 1.25rem;
        color: #2575fc;
        display: inline-block;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }


        
        .form-control, .form-select {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #2575fc;
            box-shadow: 0 0 0 0.2rem rgba(37, 117, 252, 0.1);
        }
        
    .btn-support {
        background: linear-gradient(135deg, #8a9df1 0%, #9a7cc2 100%); /* Lighter purple gradient */
        border: none;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 12px 28px;
        border-radius: 8px;
        color: white;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(138, 157, 241, 0.3);
        text-transform: uppercase;
        font-size: 0.95rem;
        border: 1px solid rgba(255,255,255,0.2); /* Subtle border for depth */
    }

    .btn-support:hover {
        background: linear-gradient(135deg, #9aacf4 0%, #ab8fce 100%); /* Lightened hover state */
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(138, 157, 241, 0.4);
        color: white;
    }


    /* Icon animation */
    .btn-support i {
        margin-right: 8px;
        transition: transform 0.3s ease-out;
    }

    .btn-support:hover i {
        transform: translateX(2px);
    }
    
    .card-body {
        padding: 2rem;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="support-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="support-card">
                    <div class="support-header">
                        <i class="fas fa-headset support-icon"></i> &nbsp; &nbsp;
                        <h2 class="support-title">How Can We Help You?</h2>
                        <p class="mb-0">We are here to help and answer any questions you might have.</p>
                    </div>
                    
                    <div class="card-body">
                        <form action="" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter your subject" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="details" class="form-label">Details</label>
                                <textarea class="form-control" id="details" name="details" rows="5" placeholder="Describe your issue in detail..." required></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="attachment" class="form-label">Attachment (Optional)</label>
                                <input class="form-control" type="file" id="attachment" name="attachment">
                                <small class="text-muted">Upload screenshots or documents if needed (Max 5MB)</small>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-support btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i> Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Add animation to form elements
        $('.form-control').each(function(i) {
            $(this).hide().delay(i * 100).fadeIn(300);
        });
        
        // Add input focus effects
        $('.form-control').focus(function() {
            $(this).parent().find('.form-label').css('color', '#2575fc');
        }).blur(function() {
            $(this).parent().find('.form-label').css('color', '#495057');
        });
    });
</script>
@endpush