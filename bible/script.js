// jaxxitabible/script.js

let bibleData = null;
let currentBook = null;
let currentChapter = 1;

// 🧠 Load Bible JSON
async function init() {
  try {
    const res = await fetch('offline-bible.json'); // 🔁 Updated filename
    bibleData = await res.json();

    const books = Object.keys(bibleData);
    const bookSelect = document.getElementById('bookSelect');

    books.forEach(book => {
      const opt = document.createElement('option');
      opt.value = book;
      opt.textContent = book;
      bookSelect.appendChild(opt);
    });

    loadChapters(); // Load chapters for the first book
  } catch (err) {
    alert('⚠️ Failed to load Bible data: ' + err.message);
    console.error(err);
  }
}

// 📚 Load chapters dynamically
function loadChapters() {
  const selectedBook = document.getElementById('bookSelect').value;
  currentBook = bibleData[selectedBook];
  currentChapter = 1;

  const scroll = document.getElementById('chapterScroll');
  scroll.innerHTML = '';

  Object.keys(currentBook).forEach(ch => {
    const btn = document.createElement('button');
    btn.textContent = ch;
    btn.className = 'chapter-btn';
    btn.onclick = () => {
      currentChapter = parseInt(ch);
      renderChapter();
    };
    scroll.appendChild(btn);
  });

  renderChapter();
}

// 📖 Render the current chapter
function renderChapter() {
  if (!currentBook) return;
  const verses = currentBook[currentChapter];
  const bookName = document.getElementById('bookSelect').value;

  document.getElementById('verse-ref').textContent = `${bookName} ${currentChapter}`;
  document.getElementById('verse-text').innerHTML = verses
    .map((v, i) => `<strong>${i + 1}.</strong> ${v}`)
    .join('<br>');
}

// 🔊 Read the chapter aloud
function speakVerse() {
  const text = document.getElementById('verse-text').innerText;
  const utterance = new SpeechSynthesisUtterance(text);
  speechSynthesis.speak(utterance);
}

// 📋 Copy current verses to clipboard
function copyVerse() {
  const text = document.getElementById('verse-text').innerText;
  navigator.clipboard.writeText(text).then(() => {
    alert("📋 Verses copied to clipboard!");
  });
}

// 🔗 Share via navigator API (if supported)
function shareVerse() {
  const text = document.getElementById('verse-text').innerText;
  if (navigator.share) {
    navigator.share({
      title: 'Jaxxita Bible Verse',
      text: text,
    }).catch(err => console.warn("Share canceled or failed", err));
  } else {
    alert("📱 Sharing is not supported in this browser.");
  }
}

// 💡 Explain selected verse via OpenAI (mock here)
function explainVerse() {
  const query = document.getElementById('verse-ref').textContent;
  const explanationBox = document.getElementById('ai-response');
  const aiText = document.getElementById('ai-text');

  aiText.textContent = `🤖 This would explain "${query}" using AI. (Connect to API here)`;
  explanationBox.style.display = 'block';
}

// 🧠 Load everything once DOM is ready
document.addEventListener('DOMContentLoaded', init);
