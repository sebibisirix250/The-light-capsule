<?php

//SESSION SECURITY

//LOGIN VERIFICATION
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0;
}

//ADMIN ACCESS VERIFICATION
function isAdmin(): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

//USER ID RETRIEVAL
function currentUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

//USER INFO RETRIEVAL FOR PERSONALIZATION
function currentUserName(): ?string
{
    return $_SESSION['user_name'] ?? null;
}
