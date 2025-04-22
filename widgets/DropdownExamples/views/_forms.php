<?php
use yii\helpers\Html;
?>

<div>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle custom-dropdown-btn" type="button" id="dropdownFormButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Dropdown form
        </button>
        <div class="dropdown-menu p-4 custom-dropdown-menu" aria-labelledby="dropdownFormButton" style="min-width: 300px;">
            <form class="custom-form">
                <div class="form-group">
                    <label for="exampleDropdownFormEmail" class="custom-label">Email address</label>
                    <input type="email" class="form-control custom-input" id="exampleDropdownFormEmail" placeholder="email@example.com">
                </div>
                <div class="form-group">
                    <label for="exampleDropdownFormPassword" class="custom-label">Password</label>
                    <input type="password" class="form-control custom-input" id="exampleDropdownFormPassword" placeholder="Password">
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input custom-checkbox" id="dropdownCheck">
                        <label class="form-check-label custom-check-label" for="dropdownCheck">
                            Remember me
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary custom-submit-btn">Sign in</button>
            </form>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item custom-link" href="#">New around here? Sign up</a>
            <a class="dropdown-item custom-link" href="#">Forgot password?</a>
        </div>
    </div>
</div>
