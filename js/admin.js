const BASE_URL = window.APP_BASE_URL || '';
const state={projects:[],services:[],editing:null,type:null};
const $=(s)=>document.querySelector(s);
const esc=(v='')=>String(v).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
async function api(url,options={}){const r=await fetch(`${BASE_URL}${url}`,options);if(r.status===401){location.href=`${BASE_URL}/admin`;throw new Error('Sesi berakhir');}const data=await r.json();if(!r.ok)throw new Error(data.message||'Terjadi kesalahan');return data}
function toast(message){$('#notice').innerHTML=`<div class="toast">${esc(message)}</div>`;setTimeout(()=>$('#notice').innerHTML='',2400)}
function page(id){document.querySelectorAll('.admin-page').forEach(x=>x.classList.toggle('active',x.id===id));document.querySelectorAll('aside nav button').forEach(x=>x.classList.toggle('active',x.dataset.page===id));$('#page-title').textContent={overview:'Ringkasan',projects:'Portofolio',services:'Layanan',profile:'Konten website'}[id]}
document.querySelectorAll('[data-page]').forEach(b=>b.onclick=()=>page(b.dataset.page));document.querySelectorAll('[data-go]').forEach(b=>b.onclick=()=>page(b.dataset.go));
async function load(){
  const [me,p,s,set]=await Promise.all([api('/api/auth/me'),api('/api/portfolio'),api('/api/services'),api('/api/settings')]);
  $('#username').textContent=me.user.username;state.projects=p.data;state.services=s.data;$('#project-count').textContent=p.data.length;$('#service-count').textContent=s.data.length;
  renderProjects();renderServices();Object.entries(set.data).forEach(([k,v])=>{const el=$(`[name="${k}"]`);if(el&&el.type!=='file')el.value=v});
  showLogo(set.data.logo);
}
function showLogo(src){$('#logo-preview').innerHTML=src?`<img src="${esc(src)}" alt="Preview logo"><span>Logo aktif</span>`:'<span>Belum ada logo custom</span>'}
function renderProjects(){$('#project-list').innerHTML=state.projects.length?state.projects.map((p,i)=>`<article class="list-item"><div class="thumb" ${p.image?`style="background:url('${esc(p.image)}') center/cover"`:''}></div><div><h3>${esc(p.title)}</h3><p>${esc(p.category)}</p></div><span>${esc(p.year||'—')}</span><span>#${String(i+1).padStart(2,'0')}</span><div class="item-actions"><button onclick="editProject(${p.id})">Edit</button><button class="danger" onclick="removeItem('portfolio',${p.id})">×</button></div></article>`).join(''):'<p>Belum ada proyek.</p>'}
function renderServices(){$('#service-list').innerHTML=state.services.length?state.services.map((s,i)=>`<article class="list-item"><div class="thumb"></div><div><h3>${esc(s.title)}</h3><p>${esc(s.description)}</p></div><span>Urutan ${s.sort_order}</span><span>#${String(i+1).padStart(2,'0')}</span><div class="item-actions"><button onclick="editService(${s.id})">Edit</button><button class="danger" onclick="removeItem('services',${s.id})">×</button></div></article>`).join(''):'<p>Belum ada layanan.</p>'}
function openEditor(type,item={}){
  state.type=type;state.editing=item.id||null;$('#modal-kicker').textContent=type==='portfolio'?'PORTOFOLIO':'LAYANAN';$('#modal-title').textContent=`${item.id?'Edit':'Tambah'} ${type==='portfolio'?'proyek':'layanan'}`;
  $('#dynamic-fields').innerHTML=type==='portfolio'?`<label>Judul<input name="title" value="${esc(item.title)}" required></label><label>Kategori<input name="category" value="${esc(item.category)}" required></label><label>Tahun<input name="year" value="${esc(item.year)}"></label><label>Urutan<input name="sort_order" type="number" value="${item.sort_order||0}"></label><label class="wide">Deskripsi<textarea name="description">${esc(item.description)}</textarea></label><label>Link proyek<input name="link" value="${esc(item.link)}"></label><label>Gambar (maks. 5MB)<input name="image" type="file" accept="image/*"></label>`:`<label class="wide">Nama layanan<input name="title" value="${esc(item.title)}" required></label><label class="wide">Deskripsi<textarea name="description" required>${esc(item.description)}</textarea></label><label>Urutan<input name="sort_order" type="number" value="${item.sort_order||0}"></label>`;
  $('#editor').showModal();
}
window.editProject=id=>openEditor('portfolio',state.projects.find(x=>x.id===id));window.editService=id=>openEditor('services',state.services.find(x=>x.id===id));
window.removeItem=async(type,id)=>{if(!confirm('Hapus konten ini?'))return;await api(`/api/admin/${type}/${id}`,{method:'DELETE'});toast('Konten dihapus');await load()};
$('#add-project').onclick=()=>openEditor('portfolio');$('#add-service').onclick=()=>openEditor('services');
$('#editor-form').addEventListener('submit',async e=>{e.preventDefault();const url=`/api/admin/${state.type}${state.editing?'/'+state.editing:''}`;const body=new FormData(e.target);if(state.editing)body.append('_method','PUT');try{await api(url,{method:'POST',body});$('#editor').close();toast('Konten berhasil disimpan');await load()}catch(err){$('#editor-error').textContent=err.message}});
$('#logo-file').addEventListener('change',e=>{const file=e.target.files[0];if(file)showLogo(URL.createObjectURL(file))});
$('#settings-form').addEventListener('submit',async e=>{e.preventDefault();const formData=new FormData(e.target);const logo=formData.get('logo_file');if(logo?.size){const upload=new FormData();upload.append('logo',logo);await api('/api/admin/settings/logo',{method:'POST',body:upload})}formData.delete('logo_file');await api('/api/admin/settings',{method:'PUT',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(formData))});toast('Konten dan logo website berhasil diperbarui');await load()});
$('#logout').onclick=async()=>{await api('/api/auth/logout',{method:'POST'});location.href=`${BASE_URL}/admin`};
load().catch(console.error);
