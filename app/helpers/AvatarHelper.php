<?php
class AvatarHelper {
    /**
     * Generate HTML for user avatar
     * @param string $profilePicture Profile picture path
     * @param string $userName User's full name
     * @param string $size Size class (sm, md, lg)
     * @param string $additionalClasses Additional CSS classes
     * @return string HTML for avatar
     */
    public static function render($profilePicture, $userName, $size = 'md', $additionalClasses = '') {
        $sizeClass = 'avatar-' . $size;
        $classes = "user-avatar {$sizeClass} {$additionalClasses}";
        
        // Determine the profile picture path
        $profilePicturePath = null;
        if ($profilePicture && !empty($profilePicture)) {
            if (!str_starts_with($profilePicture, 'http')) {
                $profilePicturePath = (str_starts_with($profilePicture, 'app/')) ? $profilePicture : 'app/uploads/' . basename($profilePicture);
            } else {
                $profilePicturePath = $profilePicture;
            }
        }
        
        // Check if file exists
        $hasValidPicture = $profilePicturePath && file_exists($profilePicturePath);
        
        if ($hasValidPicture) {
            return '<div class="' . $classes . '">
                <img src="' . htmlspecialchars($profilePicturePath) . '" alt="' . htmlspecialchars($userName) . '" class="avatar-img">
            </div>';
        } else {
            // Generate initials from name
            $initials = self::getInitials($userName);
            return '<div class="' . $classes . '">
                <div class="avatar-placeholder">
                    <span class="avatar-initials">' . htmlspecialchars($initials) . '</span>
                </div>
            </div>';
        }
    }
    
    /**
     * Get initials from full name
     * @param string $fullName User's full name
     * @return string Initials (max 2 characters)
     */
    private static function getInitials($fullName) {
        $names = explode(' ', trim($fullName));
        $initials = '';
        
        foreach ($names as $name) {
            if (!empty($name)) {
                $initials .= strtoupper(substr($name, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }
        
        return $initials ?: 'U';
    }
}
?>
