<?php

if (! function_exists("authAdmin")) {
    function authAdmin()
    {
        return auth()->user()->is_admin;
    }
}
