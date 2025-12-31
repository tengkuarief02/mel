<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melolo Stream Player</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🔥 Melolo Stream Player</h1>
        
        <!-- Search Section -->
        <div class="search-section">
            <input type="text" id="searchInput" placeholder="Cari novel/drama... (contoh: super keren)">
            <button onclick="searchVideos()">🔍 Cari</button>
        </div>

        <!-- Search Results -->
        <div id="searchResults" class="results-grid"></div>

        <!-- Video Player Section -->
        <div id="videoPlayerSection" class="player-section" style="display:none;">
            <div class="video-header">
                <button onclick="backToSearch()" class="back-btn">← Kembali</button>
                <h2 id="seriesTitle"></h2>
            </div>
            <div class="video-container">
                <video id="mainPlayer" controls width="100%" height="400"></video>
            </div>
            <div class="episodes-list">
                <h3>📺 Episode List</h3>
                <div id="episodesList"></div>
            </div>
        </div>

        <!-- Recommendations -->
        <div id="recommendations" class="results-grid" style="display:none;"></div>
    </div>

    <script>
        let currentSeriesId = null;
        const API_BASE = 'https://meloloapi-pearl.vercel.app';

        async function searchVideos() {
            const query = document.getElementById('searchInput').value.trim();
            if (!query) return;

            try {
                const response = await fetch(`api.php?action=search&query=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                displayResults(data.books || [], 'searchResults');
            } catch (error) {
                console.error('Search error:', error);
            }
        }

        function displayResults(books, containerId) {
            const container = document.getElementById(containerId);
            container.innerHTML = books.map(book => `
                <div class="video-card" onclick="loadSeries('${book.series_id}', '${book.title}')">
                    <img src="${book.thumb_url}" alt="${book.title}" loading="lazy">
                    <h4>${book.title}</h4>
                    <p>Chapter: ${book.last_chapter_index}</p>
                </div>
            `).join('');
        }

        async function loadSeries(seriesId, title) {
            currentSeriesId = seriesId;
            document.getElementById('seriesTitle').textContent = title;
            document.getElementById('searchResults').style.display = 'none';
            document.getElementById('videoPlayerSection').style.display = 'block';

            try {
                const response = await fetch(`api.php?action=details&series_id=${seriesId}`);
                const data = await response.json();
                
                displayEpisodes(data.videos || []);
                loadRecommendations();
            } catch (error) {
                console.error('Load series error:', error);
            }
        }

        function displayEpisodes(videos) {
            const container = document.getElementById('episodesList');
            container.innerHTML = videos.map((video, index) => `
                <div class="episode-item" onclick="playEpisode('${video.video_id}', ${video.chapter})">
                    <strong>Episode ${video.chapter}</strong>
                    <span>⏱️ ${video.duration}s | ❤️ ${video.digged_count}</span>
                </div>
            `).join('');
        }

        async function playEpisode(videoId, chapter) {
            try {
                const response = await fetch(`api.php?action=model&video_id=${videoId}`);
                const data = await response.json();
                
                const player = document.getElementById('mainPlayer');
                player.src = data.video_urls.main_url || data.video_urls.backup_url;
                player.load();
                player.play();
            } catch (error) {
                console.error('Play episode error:', error);
            }
        }

        async function loadRecommendations() {
            try {
                const response = await fetch('api.php?action=recommend');
                const data = await response.json();
                displayResults(data.videos || [], 'recommendations');
                document.getElementById('recommendations').style.display = 'grid';
            } catch (error) {
                console.error('Recommendations error:', error);
            }
        }

        function backToSearch() {
            document.getElementById('videoPlayerSection').style.display = 'none';
            document.getElementById('recommendations').style.display = 'none';
            document.getElementById('searchResults').style.display = 'grid';
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResults').innerHTML = '';
        }

        // Enter key search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchVideos();
        });
    </script>
</body>
</html>
