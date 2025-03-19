function deletePlatform(platformId) {
    if (confirm('Are you sure you want to delete this platform?')) {
        fetch('../ajax/handle_platform.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&platform_id=${platformId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the platform post from the DOM
                const platformElement = document.querySelector(`[data-platform-id="${platformId}"]`);
                if (platformElement) {
                    platformElement.remove();
                }
            } else {
                alert(data.message || 'Failed to delete platform');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the platform');
        });
    }
}