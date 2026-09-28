<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class LegalController extends Controller
{
    use ApiResponse;

    /**
     * Get the Privacy Policy content.
     *
     * GET /api/privacy-policy
     */
    public function privacyPolicy(): JsonResponse
    {
        $policy = [
            'title' => 'Privacy Policy',
            'last_updated' => 'July 7, 2026',
            'sections' => [
                [
                    'heading' => '1. Introduction',
                    'content' => 'Welcome to VidyaMission. We are committed to protecting your personal information and your right to privacy. If you have any questions or concerns about our policy, or our practices with regards to your personal information, please contact us.',
                ],
                [
                    'heading' => '2. Information We Collect',
                    'content' => 'We collect personal information that you provide to us, such as your name, mobile number, password, class level, district, school name, and profile photo/avatar when you register or update your student profile on the platform.',
                ],
                [
                    'heading' => '3. How We Use Your Information',
                    'content' => 'We use the collected information to set up your student profile, start your 3-day free trial, customize the educational content according to your class level, track your learning progress, award rewards, process subscriptions, and notify you about live or recorded classes.',
                ],
                [
                    'heading' => '4. Data Deletion and Retention',
                    'content' => 'You have full control over your personal data. You can delete your account at any time using the Delete Account option in the app settings, which will permanently purge all your profile details, learning progress, test attempts, rewards, and login sessions from our active databases.',
                ],
                [
                    'heading' => '5. Security of Your Information',
                    'content' => 'We implement appropriate technical and organizational security measures designed to protect the security of any personal information we process. However, please remember that no transmission over the internet is completely secure.',
                ],
                [
                    'heading' => '6. Changes to This Privacy Policy',
                    'content' => 'We may update this privacy policy from time to time. The updated version will be indicated by an updated Last Updated date and the updated policy will be effective as soon as it is accessible.',
                ],
            ],
        ];

        return $this->successResponse($policy, 'Privacy Policy retrieved successfully.');
    }

    /**
     * Get the Terms and Conditions content.
     *
     * GET /api/terms-and-conditions
     */
    public function termsAndConditions(): JsonResponse
    {
        $terms = [
            'title' => 'Terms and Conditions',
            'last_updated' => 'July 7, 2026',
            'sections' => [
                [
                    'heading' => '1. Acceptance of Terms',
                    'content' => 'By accessing and using VidyaMission, you agree to comply with and be bound by these Terms and Conditions. If you do not agree, please do not use our services.',
                ],
                [
                    'heading' => '2. Description of Services',
                    'content' => 'VidyaMission provides educational content, including recorded video classes, live sessions, PDF notes, chapter-wise tests, and reward systems tailored for West Bengal board students from class 1 to 12.',
                ],
                [
                    'heading' => '3. Account Registration & Security',
                    'content' => 'You must register for an account using a valid mobile number and OTP verification. You are responsible for maintaining the confidentiality of your account credentials and password, and for all activities that occur under your account.',
                ],
                [
                    'heading' => '4. Subscription and Payments',
                    'content' => 'We offer various paid subscription plans to access premium course content after your 3-day free trial. All payments are securely processed. Subscriptions can be reviewed, activated, or updated under the subscription dashboard.',
                ],
                [
                    'heading' => '5. Intellectual Property Rights',
                    'content' => 'All educational videos, notes, tests, layout design, graphics, and text on VidyaMission are the intellectual property of VidyaMission. You may not distribute, reproduce, or use our material for commercial purposes without explicit permission.',
                ],
                [
                    'heading' => '6. Account Termination',
                    'content' => 'We reserve the right to suspend or terminate your account if you violate any terms of service, share your account with others, or engage in unauthorized distribution of our content. You may also delete your account voluntarily at any time, which deletes your access and records permanently.',
                ],
            ],
        ];

        return $this->successResponse($terms, 'Terms and Conditions retrieved successfully.');
    }
}
