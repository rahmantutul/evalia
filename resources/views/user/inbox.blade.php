@extends('user.layouts.app')

@push('styles')
<style>
        :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --warning-gradient: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
        --glass-effect: rgba(255, 255, 255, 0.15);
        --glass-border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
</style>
@endpush
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Chat</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Creative AI </a>
                        </li><!--end nav-item-->
                        <li class="breadcrumb-item"><a href="#">Dashboard</a>
                        </li><!--end nav-item-->
                        <li class="breadcrumb-item active">Chat</li>
                    </ol>
                </div>                                
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="chat-box-left">
                <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link py-2 active" id="messages_chat_tab" data-bs-toggle="tab" href="#messages_chat" role="tab" aria-selected="true">Messages</a>
                    </li>
                </ul>                        
                <div class="chat-search p-3">
                    <div class="p-1 bg-light rounded rounded-pill">
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <button id="button-addon2" type="submit" class="btn btn-link text-secondary"><i class="fa fa-search"></i></button>
                            </div>
                            <input type="search" placeholder="Searching.." aria-describedby="button-addon2" class="form-control border-0 bg-light">
                        </div>
                    </div>
                </div>

                <div class="chat-body-left px-3 simplebar-scrollable-y" data-simplebar="init"><div class="simplebar-wrapper" style="margin: 0px -24px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;"><div class="simplebar-content" style="padding: 0px 24px;">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="messages_chat" role="tabpanel" aria-labelledby="messages_chat_tab">
                            <div class="row">
                                <div class="col">

                                    <div class="p-2 border-dashed border-theme-color rounded mb-2">
                                        <a href="" class="">
                                            <div class="d-flex align-items-start">
                                                <div class="position-relative">
                                                    <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="" class="thumb-lg rounded-circle">
                                                    <span class="position-absolute bottom-0 end-0"><i class="fa-solid fa-circle text-secondary fs-10 border-2 border-theme-color"></i></span>
                                                </div>
                                                <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                                    <h6 class="my-0 fw-medium text-dark fs-14">Shauna Jones
                                                        <small class="float-end text-muted fs-11">Yesterday</small>
                                                    </h6>
                                                    <p class="text-muted mb-0">Congratulations!</p>
                                                </div>
                                            </div>
                                        </a>                                                  
                                    </div>
                                    <div class="p-2 border-dashed border-theme-color rounded mb-2">
                                        <a href="" class="">
                                            <div class="d-flex align-items-start">
                                                <div class="position-relative">
                                                    <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="" class="thumb-lg rounded-circle">
                                                    <span class="position-absolute bottom-0 end-0"><i class="fa-solid fa-circle text-success fs-10 border-2 border-theme-color"></i></span>
                                                </div>
                                                <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                                    <h6 class="my-0 fw-medium text-dark fs-14">Frank Wei
                                                        <small class="float-end text-muted fs-11">1 Feb</small>
                                                    </h6>
                                                    <p class="text-muted mb-0"><i class="iconoir-microphone"></i> Voice message!</p>
                                                </div>
                                            </div>
                                        </a>                                                
                                    </div>
                                    <div class="p-2 border-dashed border-theme-color rounded mb-2">
                                        <a href="" class="">
                                            <div class="d-flex align-items-start">
                                                <div class="position-relative">
                                                    <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="" class="thumb-lg rounded-circle">
                                                    <span class="position-absolute bottom-0 end-0"><i class="fa-solid fa-circle text-secondary fs-10 border-2 border-theme-color"></i></span>
                                                </div>
                                                <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                                    <h6 class="my-0 fw-medium text-dark fs-14">Karen Savage
                                                        <small class="float-end text-muted fs-11">8 Feb</small>
                                                    </h6>
                                                    <p class="text-muted mb-0">Ok, I like it. 👍</p>
                                                </div>
                                            </div>
                                        </a>                                                 
                                    </div>
                                    <div class="p-2 border-dashed border-theme-color rounded mb-2">
                                        <a href="" class="">
                                            <div class="d-flex align-items-start">
                                                <div class="position-relative">
                                                    <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="" class="thumb-lg rounded-circle">
                                                    <span class="position-absolute bottom-0 end-0"><i class="fa-solid fa-circle text-secondary fs-10 border-2 border-theme-color"></i></span>
                                                </div>
                                                <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                                    <h6 class="my-0 fw-medium text-dark fs-14">Carol Maier
                                                        <small class="float-end text-muted fs-11">12 Feb</small>
                                                    </h6>
                                                    <p class="text-muted mb-0">Have a pleasure.!</p>
                                                </div>
                                            </div>
                                        </a>                                                 
                                    </div>
                                    <div class="p-2 border-dashed border-theme-color rounded mb-2">
                                        <a href="" class="">
                                            <div class="d-flex align-items-start">
                                                <div class="position-relative">
                                                    <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="" class="thumb-lg rounded-circle">
                                                    <span class="position-absolute bottom-0 end-0"><i class="fa-solid fa-circle text-secondary fs-10 border-2 border-theme-color"></i></span>
                                                </div>
                                                <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                                    <h6 class="my-0 fw-medium text-dark fs-14">Shauna Jones
                                                        <small class="float-end text-muted fs-11">15 Feb</small>
                                                    </h6>
                                                    <p class="text-muted mb-0">Congratulations!</p>
                                                </div>
                                            </div>
                                        </a>                                                
                                    </div>
                                    <div class="p-2 border-dashed border-theme-color rounded mb-2">
                                        <a href="" class="">
                                            <div class="d-flex align-items-start">
                                                <div class="position-relative">
                                                    <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="" class="thumb-lg rounded-circle">
                                                    <span class="position-absolute bottom-0 end-0"><i class="fa-solid fa-circle text-secondary fs-10 border-2 border-theme-color"></i></span>
                                                </div>
                                                <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                                    <h6 class="my-0 fw-medium text-dark fs-14">Frank Wei
                                                        <small class="float-end text-muted fs-11">2 Mar</small>
                                                    </h6>
                                                    <p class="text-muted mb-0"><i class="iconoir-microphone"></i> Voice message!</p>
                                                </div>
                                            </div>
                                        </a>                                                     
                                    </div>
                                    <div class="p-2 border-dashed border-theme-color rounded mb-2">
                                        <a href="" class="">
                                            <div class="d-flex align-items-start">
                                                <div class="position-relative">
                                                    <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="" class="thumb-lg rounded-circle">
                                                    <span class="position-absolute bottom-0 end-0"><i class="fa-solid fa-circle text-secondary fs-10 border-2 border-theme-color"></i></span>
                                                </div>
                                                <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                                    <h6 class="my-0 fw-medium text-dark fs-14">Carol Maier
                                                        <small class="float-end text-muted fs-11">14 Mar</small>
                                                    </h6>
                                                    <p class="text-muted mb-0">Have a pleasure.!</p>
                                                </div>
                                            </div>
                                        </a>                                                    
                                    </div>
                                </div>
                            </div>        
                        </div>

                    </div>
                </div></div></div></div><div class="simplebar-placeholder" style="width: 340px; height: 774px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 399px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div></div>
            </div>

            <div class="chat-box-right" style="background-color: #f6f9fb !important;">
                <div class="p-3 d-flex justify-content-between card-bg rounded" style="background: var(--primary-gradient); color: white; opacity: 1;">
                    <a href="" class="d-flex align-self-center">
                        <div class="flex-shrink-0">
                            <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="user" class="rounded-circle thumb-lg">
                        </div><!-- media-left -->
                        <div class="flex-grow-1 ms-2 align-self-center">
                            <div>
                                <h6 class="my-0 fw-medium text-white fs-20">Mary Schneider</h6>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="chat-body simplebar-scrollable-y" data-simplebar="init"><div class="simplebar-wrapper" style="margin: -16px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;"><div class="simplebar-content" style="padding: 16px;">
                    <div class="chat-detail">
                        <div class="d-flex">
                            <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="user" class="rounded-circle thumb-md">
                            <div class="ms-1 chat-box w-100">
                                <div class="user-chat">
                                    <p class="">Good Morning !</p>
                                    <p class="">There are many variations of passages of Lorem Ipsum available.</p>
                                </div>
                                <div class="chat-time">yesterday</div>
                            </div><!--end media-body--> 
                        </div><!--end media-->  
                        <div class="d-flex flex-row-reverse">
                            <img src="{{ asset('/assets/images/extra/P9aqUeKFvc.gif') }}" alt="user" class="rounded-circle thumb-md">
                            <div class="me-1 chat-box w-100 reverse">
                                <div class="user-chat">
                                    <p class="">Hi,</p>
                                    <p class="">Can be verified on any platform using docker?</p>
                                </div>
                                <div class="chat-time">12:35pm</div>
                            </div><!--end media-body--> 
                        </div><!--end media-->  
                        <div class="d-flex">
                            <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="user" class="rounded-circle thumb-md">
                            <div class="ms-1 chat-box w-100">
                                <div class="user-chat">
                                    <p class="">Have a nice day !</p>
                                    <p class="">Command was run with root privileges. I am sure about that.</p>
                                    <p class="">ok</p>
                                </div>
                                <div class="chat-time">11:10pm</div>
                            </div><!--end media-body--> 
                        </div><!--end media-->  
                        <div class="d-flex flex-row-reverse">
                            <img src="{{ asset('/assets/images/extra/P9aqUeKFvc.gif') }}" alt="user" class="rounded-circle thumb-md">
                            <div class="me-1 chat-box w-100 reverse">
                                <div class="user-chat">
                                    <p class="">Thanks for your message David. I thought I am alone with this issue. Please, 👍 the issue to support it :)</p>
                                </div>
                                <div class="chat-time">10:10pm</div>
                            </div><!--end media-body--> 
                        </div><!--end media-->  
                        <div class="d-flex">
                            <img src="{{asset('assets/images/default-avatar.jpg')}}" alt="user" class="rounded-circle thumb-md">
                            <div class="ms-1 chat-box w-100">
                                <div class="user-chat">
                                    <p class="">Sorry, I just back !</p>
                                    <p class="">It seems like you are from Mac OS world. There is no /Users/ folder on linux 😄</p>
                                </div>
                                <div class="chat-time">11:15am</div>
                            </div><!--end media-body--> 
                        </div><!--end media-->  
                        <div class="d-flex flex-row-reverse">
                            <img src="{{ asset('/assets/images/extra/P9aqUeKFvc.gif') }}" alt="user" class="rounded-circle thumb-md">
                            <div class="me-1 chat-box w-100 reverse">
                                <div class="user-chat">
                                    <p class="">Good Morning !</p>
                                    <p class="">There are many variations of passages of Lorem Ipsum available.</p>
                                </div>
                                <div class="chat-time">9:02am</div>
                            </div><!--end media-body--> 
                        </div><!--end media-->  
                    </div>  <!-- end chat-detail -->                                               
                </div></div></div></div><div class="simplebar-placeholder" style="width: 1226px; height: 724px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 402px; transform: translate3d(0px, 137px, 0px); display: block;"></div></div></div><!-- end chat-body -->
            </div>
        </div>                       
    </div>                                      
</div>
@endsection

@push('scripts')

@endpush