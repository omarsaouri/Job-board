<?php
// classes/User.php

require_once __DIR__ . '/../config/supabase.php';

class User {
    private $supabase;

    public function __construct() {
        $this->supabase = new SupabaseConfig();
    }

    public function register($email, $password, $username, $role) {
        try {
            // First, create auth user
            $authData = [
                'email' => $email,
                'password' => $password
            ];
            
            $response = $this->supabase->query('/auth/v1/signup', 'POST', $authData);
            
            if (isset($response['user']['id'])) {
                $userId = $response['user']['id'];
                
                // Create profile
                $profileData = [
                    'id' => $userId,
                    'username' => $username,
                    'role' => $role
                ];
                
                // Create profile
                $createProfileResponse = $this->supabase->query(
                    '/rest/v1/profiles',
                    'POST',
                    $profileData,
                    ['Prefer: return=minimal']
                );
                
                // Verify profile creation
                $verifyProfile = $this->supabase->query(
                    '/rest/v1/profiles?id=eq.' . $userId,
                    'GET'
                );
                
                error_log("Verification response: " . json_encode($verifyProfile));
                
                if (!empty($verifyProfile)) {
                    return true;
                } else {
                    throw new Exception("Profile creation failed - verification failed");
                }
            }
            
            return false;
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            throw $e;
        }
    }

   public function login($email, $password) {
    try {
        $loginData = [
            'email' => $email,
            'password' => $password
        ];

        $response = $this->supabase->query('/auth/v1/token?grant_type=password', 'POST', $loginData);
        
        if (isset($response['access_token'])) {
            session_start();
            $_SESSION['access_token'] = $response['access_token'];
            $_SESSION['user_id'] = $response['user']['id'];
            
            // Fetch user profile including role
            $profileResponse = $this->supabase->query(
                '/rest/v1/profiles?id=eq.' . $_SESSION['user_id'],
                'GET',
                null,
                ['Content-Type: application/json']
            );
            
            if (!empty($profileResponse) && isset($profileResponse[0])) {
                $_SESSION['username'] = $profileResponse[0]['username'];
                $_SESSION['role'] = $profileResponse[0]['role'];
                return true;
            }
        }
        return false;
    } catch (Exception $e) {
        error_log('Login error: ' . $e->getMessage());
        throw $e;
    }
    }

    public function logout() {
        session_start();
        
        try {
            if (isset($_SESSION['access_token'])) {
                $this->supabase->query('/auth/v1/logout', 'POST', [
                    'token' => $_SESSION['access_token']
                ]);
            }
            
            session_unset();
            session_destroy();
            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getUserProfile($userId) {
        try {
            $profile = $this->supabase->query(
                '/rest/v1/profiles?id=eq.' . $userId,
                'GET'
            );
            
            return !empty($profile) ? $profile[0] : null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}