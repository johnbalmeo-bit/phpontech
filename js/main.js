document.addEventListener('DOMContentLoaded', function() {
    // Initialize all interactive elements
    
    // Like buttons
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', handleLike);
    });
    
    // Save buttons
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', handleSave);
    });
    
    // Comment forms
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', handleCommentSubmit);
    });
    
    // Search form
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = this.querySelector('input[name="q"]').value.trim();
            if (query) {
                window.location.href = `explore.php?q=${encodeURIComponent(query)}`;
            }
        });
    }
});

function handleLike(e) {
    e.preventDefault();
    const btn = this;
    const recipeId = btn.dataset.recipeId;
    
    fetch('ajax_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=like&recipe_id=${recipeId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const icon = btn.querySelector('i');
            const countEl = btn.querySelector('.count') || btn.querySelector('.like-count');
            
            // Toggle icon
            if (data.action === 'like') {
                icon.classList.replace('far', 'fas');
                icon.classList.add('liked');
            } else {
                icon.classList.replace('fas', 'far');
                icon.classList.remove('liked');
            }
            
            // Update count if element exists
            if (countEl) {
                countEl.textContent = data.count || '0';
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function handleSave(e) {
    e.preventDefault();
    const btn = this;
    const recipeId = btn.dataset.recipeId;
    
    fetch('ajax_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=save&recipe_id=${recipeId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const icon = btn.querySelector('i');
            
            // Toggle icon and saved state
            if (data.action === 'save') {
                icon.classList.replace('far', 'fas');
                btn.classList.add('saved');
            } else {
                icon.classList.replace('fas', 'far');
                btn.classList.remove('saved');
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function handleCommentSubmit(e) {
    e.preventDefault();
    const form = this;
    const recipeId = form.dataset.recipeId;
    const textarea = form.querySelector('textarea');
    const content = textarea.value.trim();
    
    if (!content) return;
    
    fetch('ajax_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=comment&recipe_id=${recipeId}&content=${encodeURIComponent(content)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Add new comment to the list
            addNewComment(data.comment, form);
            
            // Update comment count
            updateCommentCount(form, data.count);
            
            // Clear the form
            textarea.value = '';
        }
    })
    .catch(error => console.error('Error:', error));
}

function addNewComment(comment, form) {
    const commentsContainer = form.closest('.recipe-comments').querySelector('.comments-list');
    const commentEl = document.createElement('div');
    commentEl.className = 'comment';
    commentEl.innerHTML = `
        <img src="${comment.user_avatar}" alt="${comment.user_name}" class="comment-avatar">
        <div class="comment-content">
            <strong>${comment.user_name}</strong>
            <p>${comment.content}</p>
            <small>${formatTime(comment.created_at)}</small>
        </div>
    `;
    
    // Add to the top of comments list
    commentsContainer.prepend(commentEl);
}

function updateCommentCount(form, count) {
    const commentCountEl = form.closest('.recipe-card').querySelector('.comment-count');
    if (commentCountEl) {
        commentCountEl.textContent = count;
    }
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}