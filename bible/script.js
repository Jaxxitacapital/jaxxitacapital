let bibleData = null;
let currentBook = null;
let currentChapter = 1;

async function init() {
  try {
    const res = await fetch('offline-bible.json');
    bibleData = await res.json();
    document.getElementById('loader')?.remove();

    const books = Object.keys(bibleData);
    const bookSelect = document.getElementById('bookSelect');

    books.forEach(book => {
      const opt = document.createElement('option');
      opt.value = book;
      opt.textContent = book;
      bookSelect.appendChild(opt);
    });

    const savedBook = localStorage.getItem('book');
    const savedChap = localStorage.getItem('chapter');

    if (savedBook) bookSelect.value = savedBook;
    loadChapters(savedBook || books[0]);

    if (savedChap) {
      currentChapter = parseInt(savedChap);
      renderChapter();
    }
  } catch (err) {
    alert("Bible failed to load: " + err.message);
  }
}

function loadChapters(book = document.getElementById('bookSelect').value) {
  currentBook = bibleData[book];
  currentChapter = 1;
  localStorage.setItem('book', book);

  const scroll = document.getElementById('chapterScroll');
  scroll.innerHTML = '';
  Object.keys(currentBook).forEach(ch => {
    const btn = document.createElement('button');
    btn.textContent = ch;
    btn.onclick = () => {
      currentChapter = parseInt(ch);
      localStorage.setItem('chapter', ch);
      renderChapter();
    };
    scroll.appendChild(btn);
  });
  renderChapter();
}

function renderChapter() {
  if (!currentBook) return;
  const lang = document.getElementById('languageSelect').value;
  const verses = currentBook[currentChapter];

  document.getElementById('verse-ref').textContent =
    `${document.getElementById('bookSelect').value} ${currentChapter}`;

  document.getElementById('verse-text').innerHTML =
    verses.map((v, i) => `<strong>${i + 1}.</strong> ${v[lang] || v.en}`).join('<br>');
}

function speakVerse() {
  const lang = document.getElementById('languageSelect').value;
  const text = document.getElementById('verse-text').innerText;
  const utterance = new SpeechSynthesisUtterance(text);
  if (lang === "sw") utterance.lang = "sw-KE";
  speechSynthesis.speak(utterance);
}

function copyVerse() {
  navigator.clipboard.writeText(document.getElementById('verse-text').innerText)
    .then(() => alert("✅ Verses copied!"));
}

function shareVerse() {
  const text = document.getElementById('verse-text').innerText;
  if (navigator.share) {
    navigator.share({ title: 'Bible Verse', text });
  } else {
    alert("Sharing not supported in this browser.");
  }
}

function searchVerse() {
  const input = document.getElementById('searchInput').value.toLowerCase();
  const lang = document.getElementById('languageSelect').value;

  for (const [book, chapters] of Object.entries(bibleData)) {
    for (const [ch, verses] of Object.entries(chapters)) {
      for (let i = 0; i < verses.length; i++) {
        const verseText = verses[i][lang]?.toLowerCase() || "";
        if (verseText.includes(input)) {
          document.getElementById('bookSelect').value = book;
          loadChapters(book);
          currentChapter = parseInt(ch);
          renderChapter();
          return;
        }
      }
    }
  }
  alert("❌ Verse not found.");
}

function filterByTag(tag) {
  const lang = document.getElementById('languageSelect').value;
  const book = document.getElementById('bookSelect').value;
  const verses = bibleData[book][currentChapter];

  const filtered = verses.filter(v => v.tags?.includes(tag));
  document.getElementById('verse-text').innerHTML =
    filtered.length ? filtered.map((v, i) =>
      `<strong>${i + 1}.</strong> ${v[lang] || v.en}`).join('<br>') :
      "❌ Hakuna mistari kwa tag hiyo.";
}

function resetFilter() {
  renderChapter();
}

document.addEventListener('DOMContentLoaded', init);
