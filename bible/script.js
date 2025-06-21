// script.js

let bibleData = null;
let currentBook = null;
let currentChapter = 1;

async function init() {
  try {
    const res = await fetch('bible.json');
    bibleData = await res.json();

    const books = Object.keys(bibleData);
    const bookSelect = document.getElementById('bookSelect');

    books.forEach(book => {
      const opt = document.createElement('option');
      opt.value = book;
      opt.textContent = book;
      bookSelect.appendChild(opt);
    });
    loadChapters(); // load chapters for first book
    renderChapter();
  } catch (err) {
    alert('⚠️ Failed to load Bible data: ' + err);
  }
}

function loadChapters() {
  const book = document.getElementById('bookSelect').value;
  currentBook = bibleData[book];
  currentChapter = 1;
  const scroll = document.getElementById('chapterScroll');
  scroll.innerHTML = '';
  Object.keys(currentBook).forEach(ch => {
    const btn = document.createElement('button');
    btn.textContent = ch;
    btn.onclick = () => { currentChapter = +ch; renderChapter(); };
    scroll.appendChild(btn);
  });
  renderChapter();
}

function renderChapter() {
  if (!currentBook) return;
  const verses = currentBook[currentChapter];
  document.getElementById('verse-ref').textContent =
    `${document.getElementById('bookSelect').value} ${currentChapter}`;
  document.getElementById('verse-text').innerHTML =
    verses.map((v,i) => `<strong>${i+1}.</strong> ${v}`).join('<br>');
}

document.addEventListener('DOMContentLoaded', init);
