<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About PostPro</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #8bc6ec 0%, #9599e2 100%);
      color: #222;
    }

  .top-links{
      position: absolute; 
      top: 25px;
      right: 720px;
    }

  .top-links a {
      margin-left: 23px;
      color: white;
      font-weight:750;
      font-size: 1.3rem;
    }

  a {
      background-image: linear-gradient(
      to right,
    #6a5af9,
    #6a5af9 50%,
    #ffffff 50%
      );
      background-size: 200% 100%;
      background-position: -100%;
      display: inline-block;
      padding: 5px 0;
      position: relative;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      transition: all 0.3s ease-in-out;
      text-decoration: underline;
    }

  a:before{
      content: '';
      background: #6a5af9;
      display: block;
      position: absolute;
      bottom: -3px;
      left: 0;
      width: 0;
      height: 3px;
      transition: all 0.3s ease-in-out;
      text-decoration: underline;
    }

  a:hover {
      background-position: 0;
    }

  a:hover::before{
      width: 100%;
    }

    .container {
      max-width: 760px;
      margin: 80px auto;
      background: white;
      border-radius: 14px;
      padding: 30px 30px 40px;
      box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }

    h1 {
      color: #6a5af9;
      text-align: center;
      margin-bottom: 20px;
      font-weight: 700;
    }

    .tabs {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 25px;
    }

    .tab-btn {
      background: #f0f0f0;
      border-radius: 25px;
      padding: 12px 28px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      color: #6a5af9;
      border: none;
    }

    .tab-btn.active {
      background: #6a5af9;
      color: white;
      box-shadow: 0 8px 20px rgba(106, 90, 249, 0.4);
    }

    .tab-content {
      display: none;
      line-height: 1.6;
      font-size: 1.1rem;
      color: #333;
    }

    .tab-content.active {
      display: block;
    }

    .counter {
      background: #f5f3fe;
      color: #333;
      border-radius: 14px;
      padding: 25px 30px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      margin-top: 25px;
      position: relative;
    }

    .counter h2 {
      font-size: 2.6rem;
      margin: 0 0 8px;
      font-weight: 800;
      color: #6a5af9;
    }

    .counter p {
      font-weight: 600;
      font-size: 1rem;
      color: #444;
      margin-bottom: 18px;
    }

    .progress-bar {
      width: 100%;
      height: 14px;
      background: #e6e6f9;
      border-radius: 12px;
      overflow: hidden;
      position: relative;
      box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .progress-fill {
      height: 100%;
      width: 0;
      background: linear-gradient(90deg, #6a5af9, #8f7dff);
      border-radius: 12px 0 0 12px;
      transition: width 2.5s ease-out;
      box-shadow: 0 0 8px rgba(106, 90, 249, 0.5);
    }

    .progress-bar.empty .progress-fill {
      background:#6a5af9,#8f7dff;
      box-shadow: none;
    }

    .tooltip {
      position: absolute;
      top: -28px;
      left: 0;
      font-size: 0.75rem;
      font-weight: 600;
      color: #555;
      padding: 2px 8px;
      background: #fff;
      border-radius: 6px;
      box-shadow: 0 0 6px rgba(0,0,0,0.15);
      white-space: nowrap;
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .progress-bar:hover .tooltip {
      opacity: 1;
    }

    .more-coming {
      margin-top: 12px;
      font-size: 0.9rem;
      font-weight: 500;
      color: #6a5af9;
      display: none;
      opacity: 0;
      animation: fadeIn 1s ease forwards;
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }

    @media (max-width: 480px) {
      .container {
        padding: 30px 20px 40px;
      }
      .counter h2 {
        font-size: 2rem;
      }
 

    }
      ol li::marker {
  font-weight: bold;
}
  </style>
</head>
<body>

<div class="top-links">
  <a href="postpro.php">HomePage</a>
  <a href="feedback.php">FeedBack</a>
</div>
<div class="container">
  <h1>About PostPro</h1>

  <div class="tabs">
    <button class="tab-btn active" aria-controls="mission">Our Mission</button>
    <button class="tab-btn" aria-controls="features">Features</button>
    <button class="tab-btn" aria-controls="howto">How to Use</button>
  </div>

  <section id="mission" class="tab-content active">
    <p>At <strong>PostPro</strong>, we empower social media enthusiasts with trending captions, hashtags, and song suggestions to make every post shine! Our mission is to help you engage your audience with the best, carefully curated content tailored to your mood and style.</p>

    <!-- COUNTERS ONLY IN OUR MISSION -->
    <div class="counters">
      <div class="counter">
        <h2 id="captionsCount">0</h2>
        <p>Captions Generated</p>
        <div class="progress-bar">
          <div class="progress-fill" id="progressCaptions"></div>
          <div class="tooltip" id="tooltipCaptions">0%</div>
        </div>
        <div class="more-coming" id="comingCaptions">🎉 More captions coming soon...</div>
      </div>

      <div class="counter">
        <h2 id="hashtagsCount">0</h2>
        <p>Hashtags Suggested</p>
        <div class="progress-bar">
          <div class="progress-fill" id="progressHashtags"></div>
          <div class="tooltip" id="tooltipHashtags">0%</div>
        </div>
        <div class="more-coming" id="comingHashtags">📈New hashtags coming soon!</div>
      </div>

      <div class="counter">
        <h2 id="songsCount">Coming Soon</h2>
        <p>Songs Suggestions</p>
        <div class="progress-bar empty">
          <div class="progress-fill" id="progressSongs"></div>
          <div class="tooltip" id="tooltipSongs">0%</div>
        </div>
        <div class="more-coming" id="comingSongs">🎵 Songs feature launching soon!</div>
      </div>
    </div>
  </section>

  <section id="features" class="tab-content">
    <ul>
      <li>Generate captions tailored to your mood or photo.</li>
      <li>Find trending hashtags to increase your post reach.</li>
      <li>Get song suggestions to match your vibe.</li>
      <li>Save your favorite captions, hashtags, and songs in one place.</li>
      <li>Simple, user-friendly interface with quick copy & favorite buttons.</li>
      <li>Upload a photo, select your mood, and let PostPro generate suggestions and make a best generated post.</li>
    </ul>
  </section>

  <section id="howto" class="tab-content">
    <ol>
      <li><strong>Upload a photo</strong> that captures your current mood or moment.</li>
      <li><strong>Select a mood</strong> from our predefined options.</li>
      <li><strong>Get instant suggestions</strong> for captions, hashtags, and soon songs that perfectly match your vibe.</li>
      <li>You can also generate captions, hashtags, or songs separately on their dedicated pages.</li>
      <li><strong>Copy your favorites</strong> or add them to your <em>Favorites</em> page for quick access later.</li>
      <li><strong>You can also have caption hashtag and song which matches with your amazing uploaded photo</strong></li>
     
      <li>This is a fully self-contained system — similar to searching moods and songs on Instagram but optimized for your personal post style.</li>
    </ol>
    <p><strong>Note:</strong> The song suggestion feature is powered by static AI logic and is continuously improving!
  </section>
</div>

<script>
  // Tabs
  const tabs = document.querySelectorAll('.tab-btn');
  const contents = document.querySelectorAll('.tab-content');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));
      tab.classList.add('active');
      document.getElementById(tab.getAttribute('aria-controls')).classList.add('active');
    });
  });

  // Animate count & progress
  function animateCountAndProgress(idCount, idProgress, idTooltip, count, maxCount, isPending = false, msgId = null) {
    const countEl = document.getElementById(idCount);
    const progressEl = document.getElementById(idProgress);
    const tooltipEl = document.getElementById(idTooltip);
    const messageEl = msgId ? document.getElementById(msgId) : null;

    if (isPending) {
      countEl.textContent = "Coming Soon";
      progressEl.style.width = '0%';
      tooltipEl.textContent = '0%';
      progressEl.parentElement.classList.add('empty');
      if (messageEl) messageEl.style.display = 'block';
      return;
    }

    let startTime = null;
    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      let elapsed = timestamp - startTime;
      let progress = Math.min(elapsed / 2500, 1);
      let currentCount = Math.floor(progress * count);
      countEl.textContent = currentCount.toLocaleString();

      let widthPercent = Math.min((currentCount / maxCount) * 100, 100);
      progressEl.style.width = widthPercent + '%';
      tooltipEl.textContent = Math.floor(widthPercent) + '%';

      if (progress < 1) {
        window.requestAnimationFrame(step);
      } else {
        if (messageEl) messageEl.style.display = 'block';
      }
    }
    window.requestAnimationFrame(step);
  }

  // Animate counters only once page loads
  window.addEventListener('DOMContentLoaded', () => {
    animateCountAndProgress('captionsCount', 'progressCaptions', 'tooltipCaptions', 3175, 12500, false, 'comingCaptions');
    animateCountAndProgress('hashtagsCount', 'progressHashtags', 'tooltipHashtags', 5068, 12500, false, 'comingHashtags');
    animateCountAndProgress('songsCount', 'progressSongs', 'tooltipSongs', 1217, 10000, false, 'comingSongs');
  });
</script>

</body>
</html>
