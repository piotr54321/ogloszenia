<div class="col-1"></div>
<div class="row">
    <div class="col-5"></div>
    <div class="col-2">
        <div class="container">
            <div class="jumbotron p-3 p-md-2 text-white rounded bg-dark">
                <div class="col-md-13 px-0">
                    <h6 align="center" class="display-12 font-italic">
                        Edit password
                    </h6>
                </div>
            </div>
            <hr class="featurette-divider">
            <form method="post">
                <div class="form-group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-key">
                        <path
                            d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path>
                    </svg>
                    <label for="oldpassword">Old password</label>
                    <input type="password" class="form-control" name="oldpassword" id="oldpassword"/>
                </div>
                <span class="text-danger"><?php echo form_error('oldpassword'); ?></span>
                <div class="form-group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-key">
                        <path
                            d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path>
                    </svg>
                    <label for="newpassword">New password</label>
                    <input type="password" class="form-control" name="newpassword" id="newpassword"/>
                </div>
                <span class="text-danger"><?php echo form_error('newpassword'); ?></span>
                <div class="form-group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-key">
                        <path
                            d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path>
                    </svg>
                    <label for="confirmpassword">New password confirm</label>
                    <input type="password" class="form-control" name="confirmpassword" id="confirmpassword"/>
                </div>
                <span class="text-danger"><?php echo form_error('confirmpassword'); ?></span>
                <div>
                    <hr class="featurette-divider">
                </div>
                <div align="center">
                    <button type="submit" name="insert" value="Signup" class="btn btn-outline-warning">
                        Change password
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-alert-circle">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </button>
                </div>
                <div align="center">
                    <br>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-arrow-left-circle">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 8 8 12 12 16"></polyline>
                        <line x1="16" y1="12" x2="8" y2="12"></line>
                    </svg>
                    <a href="<?php echo base_url() ?>home/index">Return page user</a>
                    <br>
                </div>
            </form>
        </div>
    </div>
    <div class="col-5"></div>
</div>
<div class="col-1"></div>
