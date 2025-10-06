@extends('layouts.app')

@section('title', 'Anime Search')

@section('content')
  <h1 class="mb-4">Cari Anime</h1>

  <div class="row g-2 mb-4">
    <div class="col-md-6">
      <input id="q" class="form-control" placeholder="contoh: one piece / naruto">
    </div>
    <div class="col-md-6">
      <button class="btn btn-primary me-2" onclick="search()">Cari</button>
      <button class="btn btn-outline-light" onclick="loadTop()">Top Anime</button>
    </div>
  </div>

  <p id="status" class="text-secondary small"></p>

  <div id="list" class="row g-4"></div>
@endsection

@push('scripts')
<script>
async function search(page=1){
  const q=document.getElementById('q').value.trim();
  if(!q) return alert('Masukkan kata kunci');
  setStatus('Mencari…');
  const res = await fetch(`/api/anime/search?q=${encodeURIComponent(q)}&page=${page}`);
  const json = await res.json();
  render(json, page, 'search');
}

async function loadTop(page=1){
  setStatus('Memuat Top…');
  const res = await fetch(`/api/anime/top?page=${page}`);
  const json = await res.json();
  render(json, page, 'top');
}

function setStatus(msg){ document.getElementById('status').textContent = msg || ''; }

async function showDetail(id){
  setStatus('Memuat detail…');
  const res = await fetch(`/api/anime/${id}`);
  const d = await res.json();
  const info = `${d.title}\nScore: ${d.score ?? '-'}\nEpisodes: ${d.episodes ?? '-'}\nGenres: ${(d.genres||[]).join(', ')}`;
  alert(info);
  setStatus('');
}

function render(json, page, mode){
  setStatus('');
  const list=document.getElementById('list');
  list.innerHTML='';
  const items=json.data || [];
  if(items.length===0){list.innerHTML='<p class="text-muted">Tidak ada hasil.</p>';return;}

  // tampilkan anime
  items.forEach(a=>{
    const col=document.createElement('div');
    col.className='col-md-3';
    col.innerHTML=`
      <div class="card h-100 shadow-sm">
        <img src="${a.cover||''}" class="card-img-top" style="height:320px;object-fit:cover">
        <div class="card-body">
          <h5 class="card-title">${a.title}</h5>
          <p class="card-text small mb-2">${a.type ?? ''} • ${a.year ?? ''}</p>
          <div class="d-flex justify-content-between align-items-center">
            <span class="badge bg-primary">⭐ ${a.score ?? '-'}</span>
            <button class="btn btn-sm btn-light" onclick="showDetail(${a.id})">Detail</button>
          </div>
        </div>
      </div>`;
    list.appendChild(col);
  });

  // ===== Pagination =====
  const pagination = json.pagination || {};
  const last = pagination.last_visible_page || 1;
  const hasNext = pagination.has_next_page;
  const container = document.createElement('div');
  container.className = 'd-flex justify-content-center my-4';

  let html = `<nav><ul class="pagination pagination-sm">`;

  // tombol prev
  if (page > 1) {
    html += `<li class="page-item"><a class="page-link" href="#" onclick="${mode==='top'?'loadTop':'search'}(${page-1})">&laquo;</a></li>`;
  }

  // nomor halaman
  for(let i = Math.max(1, page-2); i <= Math.min(last, page+2); i++){
    html += `<li class="page-item ${i===page?'active':''}">
               <a class="page-link" href="#" onclick="${mode==='top'?'loadTop':'search'}(${i})">${i}</a>
             </li>`;
  }

  // tombol next
  if (hasNext && page < last) {
    html += `<li class="page-item"><a class="page-link" href="#" onclick="${mode==='top'?'loadTop':'search'}(${page+1})">&raquo;</a></li>`;
  }

  html += `</ul></nav>`;
  container.innerHTML = html;
  list.appendChild(container);
}

loadTop();
</script>
@endpush
