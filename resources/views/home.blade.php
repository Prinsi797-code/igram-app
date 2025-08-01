@extends('layouts.index')

@section('title', 'Home')
@section('content')
    <div class="header">
        <h1>Instagram Downloader</h1>
        <p>Download Video, Reels, Photo, IGTV, carousel from Instagram</p>
    </div>

    <div class="search-container">
        <div class="search-box">
            <input type="text" class="search-input" placeholder="Enter Instagram username..." id="usernameInput">
            <button class="clear-btn" id="clearBtn" style="display: none;">✕</button>
            <button class="download-btn" id="searchBtn">Search</button>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div id="loadingState" class="loading" style="display: none;">
                <div class="spinner"></div>
                <h3>Fetching profile data...</h3>
                <p>Please wait while we retrieve the Instagram profile information.</p>
            </div>

            <div id="searchResults" style="display: none;">
                <div class="search-result">
                    <div class="profile-header">
                        <div class="profile-pic">
                            <img id="profilePic" src="" alt="Profile Picture">
                            <div class="verification-badge" id="verificationBadge" style="display: none;">✓</div>
                        </div>
                        <h2 class="profile-name" id="profileName"></h2>
                        <p class="profile-username" id="profileUsername"></p>
                        <p class="profile-bio" id="profileBio"></p>
                        <div class="profile-stats">
                            <div class="stat">
                                <span class="stat-number" id="postsCount">0</span>
                                <span class="stat-label">posts</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number" id="followersCount">0</span>
                                <span class="stat-label">followers</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number" id="followingCount">0</span>
                                <span class="stat-label">following</span>
                            </div>
                        </div>
                    </div>

                    <div class="tabs">
                        <button class="tab active">POSTS</button>
                        <button class="tab">STORIES</button>
                        <button class="tab">HIGHLIGHTS</button>
                        <button class="tab">REELS</button>
                    </div>

                    <div class="posts-grid" id="postsGrid">
                        <!-- Posts will be populated here -->
                    </div>
                </div>
            </div>

            <div id="errorState" class="error" style="display: none;">
                <h3>Profile Not Found</h3>
                <p>The username you entered could not be found or the profile is private.</p>
            </div>
        </div>
    </div>

    <script>
        class InstagramDownloader {
            constructor() {
                this.initializeElements();
                this.bindEvents();
            }

            initializeElements() {
                this.usernameInput = document.getElementById('usernameInput');
                this.clearBtn = document.getElementById('clearBtn');
                this.searchBtn = document.getElementById('searchBtn');
                this.loadingState = document.getElementById('loadingState');
                this.searchResults = document.getElementById('searchResults');
                this.errorState = document.getElementById('errorState');
                this.postsGrid = document.getElementById('postsGrid');
            }

            bindEvents() {
                this.searchBtn.addEventListener('click', () => this.searchProfile());
                this.usernameInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.searchProfile();
                });
                this.usernameInput.addEventListener('input', () => this.handleInputChange());
                this.clearBtn.addEventListener('click', () => this.clearInput());
            }

            handleInputChange() {
                const hasValue = this.usernameInput.value.trim().length > 0;
                this.clearBtn.style.display = hasValue ? 'block' : 'none';
            }

            clearInput() {
                this.usernameInput.value = '';
                this.clearBtn.style.display = 'none';
                this.hideAllStates();
            }

            hideAllStates() {
                this.loadingState.style.display = 'none';
                this.searchResults.style.display = 'none';
                this.errorState.style.display = 'none';
            }

            async searchProfile() {
                const username = this.usernameInput.value.trim();
                if (!username) return;

                this.hideAllStates();
                this.loadingState.style.display = 'block';
                this.searchBtn.disabled = true;
                this.searchBtn.textContent = 'Searching...';

                try {
                    // Use Laravel route instead of direct API call
                    const response = await fetch(`/api/profile?username=${encodeURIComponent(username)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || ''
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.displayProfile(data);
                    } else {
                        this.showError(data.message || 'Profile not found');
                    }
                } catch (error) {
                    console.error('Error fetching profile:', error);
                    this.showError('Network error. Please try again.');
                } finally {
                    this.loadingState.style.display = 'none';
                    this.searchBtn.disabled = false;
                    this.searchBtn.textContent = 'Search';
                }
            }

            displayProfile(data) {
                // Update profile information with proxy for profile picture
                const profilePic = document.getElementById('profilePic');

                // Use Laravel proxy for profile picture
                if (data.profile_pic_url) {
                    const proxyUrl = `/api/proxy-image?url=${encodeURIComponent(data.profile_pic_url)}`;
                    profilePic.src = proxyUrl;
                    profilePic.onerror = () => {
                        // Fallback to default avatar
                        profilePic.src =
                            'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNjAiIGN5PSI2MCIgcj0iNjAiIGZpbGw9IiNGMEYwRjAiLz48Y2lyY2xlIGN4PSI2MCIgY3k9IjQ1IiByPSIyMCIgZmlsbD0iI0MwQzBDMCIvPjxwYXRoIGQ9Ik0yMCA5NUMyMCA4MCAzNS44IDY1IDYwIDY1Qzg0LjIgNjUgMTAwIDgwIDEwMCA5NVYxMjBIMjBWOTVaIiBmaWxsPSIjQzBDMEMwIi8+PC9zdmc+';
                    };
                }

                document.getElementById('profileName').textContent = data.full_name || data.username;
                document.getElementById('profileUsername').textContent = `@${data.username}`;
                document.getElementById('profileBio').textContent = data.biography || '';
                document.getElementById('postsCount').textContent = this.formatNumber(data.posts_count);
                document.getElementById('followersCount').textContent = this.formatNumber(data.followers_count);
                document.getElementById('followingCount').textContent = this.formatNumber(data.following_count);

                // Show verification badge if verified
                const verificationBadge = document.getElementById('verificationBadge');
                verificationBadge.style.display = data.is_verified ? 'flex' : 'none';

                // Display posts
                this.displayPosts(data.recent_posts || []);

                this.searchResults.style.display = 'block';
            }

            displayPosts(posts) {
                this.postsGrid.innerHTML = '';

                if (posts.length === 0) {
                    this.postsGrid.innerHTML =
                        '<p style="text-align: center; color: #666; grid-column: 1/-1;">No posts available</p>';
                    return;
                }

                posts.forEach(post => {
                    const postCard = this.createPostCard(post);
                    this.postsGrid.appendChild(postCard);
                });
            }

            createPostCard(post) {
                const card = document.createElement('div');
                card.className = 'post-card';

                const timeAgo = this.getTimeAgo(post.taken_at_timestamp);

                // Use Laravel proxy for post images
                const proxyImageUrl = `/api/proxy-image?url=${encodeURIComponent(post.display_url)}`;

                card.innerHTML = `
            <div class="post-image-container">
                <img src="${proxyImageUrl}" alt="${post.is_video ? 'Story Video' : 'Story/Post Image'}" class="post-image" loading="lazy" 
                 onerror="this.parentElement.innerHTML='<div class=\\"image-placeholder\\"><div style=\\"text-align: center;\\">${post.is_video ? '📹' : '📷'}<span style=\\"display: block; margin-top: 5px; font-size: 0.8rem;\\">${post.is_video ? 'Video Preview' : 'Image Preview'}</span></div></div>
            <div class="post-overlay">${post.is_video ? '📹 Video' : '📷 Photo'}</div>
            </div>
        
            <div class="post-actions">
                <div class="post-stats">
                    <div class="post-stat">
                        <span>❤️</span>
                        <span>${this.formatNumber(post.likes_count)}</span>
                    </div>
                    <div class="post-stat">
                        <span>💬</span>
                        <span>${this.formatNumber(post.comments_count)}</span>
                    </div>
                    <div class="post-stat">
                        <span>🕒</span>
                        <span>${timeAgo}</span>
                    </div>
                </div>
                <button class="post-download" onclick="downloadPost('${post.display_url}', '${post.shortcode}', ${post.is_video})">
                    Download ${post.is_video ? 'Video' : 'Image'}
                </button>
                ${post.caption ? `<div class="post-caption">${post.caption}</div>` : ''}
            </div>
        `;
                return card;
            }

            formatNumber(num) {
                if (num >= 1000000) {
                    return (num / 1000000).toFixed(1) + 'M';
                } else if (num >= 1000) {
                    return (num / 1000).toFixed(1) + 'K';
                }
                return num.toString();
            }

            getTimeAgo(timestamp) {
                const now = new Date().getTime() / 1000;
                const diff = now - timestamp;

                if (diff < 3600) {
                    return Math.floor(diff / 60) + 'm ago';
                } else if (diff < 86400) {
                    return Math.floor(diff / 3600) + 'h ago';
                } else if (diff < 604800) {
                    return Math.floor(diff / 86400) + 'd ago';
                } else {
                    return Math.floor(diff / 604800) + 'w ago';
                }
            }

            showError(message = 'Profile not found') {
                this.hideAllStates();
                this.errorState.style.display = 'block';
                this.errorState.innerHTML = `
            <h3>Error</h3>
            <p>${message}</p>
        `;
            }
        }

        // Download functionality using Laravel proxy
        async function downloadPost(url, shortcode, isVideo) {
            const downloadBtn = event.target;
            const originalText = downloadBtn.textContent;

            try {
                downloadBtn.textContent = 'Downloading...';
                downloadBtn.disabled = true;

                // Use Laravel download route
                const downloadUrl = `/api/download?url=${encodeURIComponent(url)}&filename=instagram_${shortcode}`;

                // Create invisible link and trigger download
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = `instagram_${shortcode}.${isVideo ? 'mp4' : 'jpg'}`;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                downloadBtn.textContent = 'Downloaded!';
                setTimeout(() => {
                    downloadBtn.textContent = originalText;
                    downloadBtn.disabled = false;
                }, 2000);

            } catch (error) {
                console.error('Download failed:', error);
                downloadBtn.textContent = 'Download Failed';
                setTimeout(() => {
                    downloadBtn.textContent = originalText;
                    downloadBtn.disabled = false;
                }, 2000);

                // Fallback: open in new tab
                window.open(url, '_blank');
            }
        }

        // Initialize the app
        document.addEventListener('DOMContentLoaded', () => {
            new InstagramDownloader();
        });
    </script>
@endsection
