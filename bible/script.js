const bibleFile = 'bible_offline.json';
let bibleData = {};
let currentBook = 'Genesis';
let currentChapter = '1';
let currentLanguage = 'en';
let currentVerseRef = '';
let dailyVerseKey = 'jaxxitaDailyVerse';

window.onload = async () => {
  await loadBibleData();
  populateBooks();
  loadDailyVerse();
};

async function loadBibleData() {
  const res = await fetch(bibleFile);
  bibleData = await res.json();
}

function populateBooks() {
  const bookSelect = document.getElementById('bookSelect');
  bookSelect.innerHTML = '';
  for (let book in bibleData) {
    const opt = document.createElement('option');
    opt.value = book;
    opt.textContent = book;
    bookSelect.appendChild(opt);
  }
  loadChapters();
}

function loadChapters() {
  const book = document.getElementById('bookSelect').value;
  const chapterScroll = document.getElementById('chapterScroll');
  chapterScroll.innerHTML = '';
  currentBook = book;
  const chapters = Object.keys(bibleData[book]);
  chapters.forEach(ch => {
    const btn = document.createElement('button');
    btn.textContent = ch;
    btn.onclick = () => loadChapter(book, ch);
    chapterScroll.appendChild(btn);
  });
  loadChapter(book, chapters[0]);
}

function loadChapter(book, chapter) {
  currentChapter = chapter;
  const verses = bibleData[book][chapter];
  const ref = `${book} ${chapter}`;
  currentVerseRef = ref;
  const verseText = Object.entries(verses)
    .map(([v, txt]) => `<strong>${v}</strong>. ${txt}`)
    .join('<br/>');
  document.getElementById('verse-ref').textContent = ref;
  document.getElementById('verse-text').innerHTML = verseText;
  document.getElementById('verse-image').src = chooseImage(ref);
  saveDailyVerse(ref);
}

function chooseImage(ref) {
  if (ref.includes("Proverbs")) return 'images/success.jpg';
  if (ref.includes("Genesis")) return 'images/blessing.jpg';
  if (ref.includes("Ecclesiastes")) return 'images/journey.jpg';
  return 'images/faith.jpg';
}

function speakVerse() {
  const text = document.getElementById('verse-text').innerText;
  const utterance = new SpeechSynthesisUtterance(text);
  utterance.lang = currentLanguage === 'sw' ? 'sw-KE' : 'en-US';
  speechSynthesis.speak(utterance);
}

function copyVerse() {
  const verse = document.getElementById('verse-text').innerText;
  navigator.clipboard.writeText(`${currentVerseRef}\n${verse}`);
  alert('Verse copied!');
}

function shareVerse() {
  const ref = document.getElementById('verse-ref').innerText;
  const text = document.getElementById('verse-text').innerText;
  const url = `${location.href}#${ref.replace(" ", "-")}`;
  const shareText = `${ref}\n${text}\n${url}`;
  if (navigator.share) {
    navigator.share({ title: 'Bible Verse', text: shareText });
  } else {
    prompt('Copy & share:', shareText);
  }
}

function reloadCurrent() {
  loadChapter(currentBook, currentChapter);
}

function saveDailyVerse(ref) {
  localStorage.setItem(dailyVerseKey, JSON.stringify({ ref, date: new Date().toDateString() }));
}

function loadDailyVerse() {
  const saved = JSON.parse(localStorage.getItem(dailyVerseKey));
  const today = new Date().toDateString();
  if (saved && saved.date === today) {
    const [book, chapter] = saved.ref.split(' ');
    document.getElementById('bookSelect').value = book;
    loadChapters();
    setTimeout(() => loadChapter(book, chapter), 300);
  } else {
    const firstBook = Object.keys(bibleData)[0];
    document.getElementById('bookSelect').value = firstBook;
    loadChapters();
  }
}

async function explainVerse() {
  const text = document.getElementById('verse-text').innerText;
  const explanation = await askChatGPT(`Explain this Bible passage: ${text}`);
  document.getElementById('ai-text').textContent = explanation;
  document.getElementById('ai-response').style.display = 'block';
}

async function askChatGPT(promptText) {
  const response = await fetch("https://api.openai.com/v1/chat/completions", {
    method: "POST",
    headers: {
      "Authorization": "Bearer YOUR_OPENAI_API_KEY",
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      model: "gpt-3.5-turbo",
      messages: [{ role: "user", content: promptText }]
    })
  });
  const data = await response.json();
  return data.choices?.[0]?.message?.content || "No explanation found.";
}
