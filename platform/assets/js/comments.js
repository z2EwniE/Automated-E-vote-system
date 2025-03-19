// Function to load comments for a platform
function loadComments(platformId) {
    fetch(`../ajax/get_comments.php?platform_id=${platformId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const commentsContainer = document.querySelector(`[data-platform-id="${platformId}"] .comments-container`);
                commentsContainer.innerHTML = data.comments.map(comment => `
                    <div class="comment-item p-4 border-t border-purple-500/20">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">${comment.student_name.charAt(0)}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white">${comment.student_name}</p>
                                <p class="text-sm text-gray-300">${comment.content}</p>
                                <p class="text-xs text-gray-400 mt-1">${comment.created_at}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(error => console.error('Error loading comments:', error));
}

// Function to add a new comment
function addComment(platformId, content) {
    fetch('../ajax/handle_comment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            platform_id: platformId,
            content: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear the comment input
            const commentInput = document.querySelector(`[data-platform-id="${platformId}"] .comment-input`);
            commentInput.value = '';
            
            // Reload comments
            loadComments(platformId);
        } else {
            alert(data.message || 'Failed to add comment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the comment');
    });
}

// Initialize comments for all platforms
document.addEventListener('DOMContentLoaded', () => {
    const platforms = document.querySelectorAll('[data-platform-id]');
    platforms.forEach(platform => {
        const platformId = platform.dataset.platformId;
        loadComments(platformId);

        // Add submit handler for comment form
        const commentForm = platform.querySelector('.comment-form');
        commentForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const commentInput = commentForm.querySelector('.comment-input');
            const content = commentInput.value.trim();
            if (content) {
                addComment(platformId, content);
            }
        });
    });
});