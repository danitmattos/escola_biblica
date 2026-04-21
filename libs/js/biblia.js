// libs/js/biblia.js — Integração com bible-api.com
(function(){
  if(!document.getElementById('biblia-livro')) return;

  // Lista simplificada de livros (pode ser expandida)
  var livros = [
    {nome:'Gênesis', sigla:'genesis', caps:50},
    {nome:'Êxodo', sigla:'exodo', caps:40},
    {nome:'Levítico', sigla:'levitico', caps:27},
    {nome:'Números', sigla:'numeros', caps:36},
    {nome:'Deuteronômio', sigla:'deuteronomio', caps:34},
    {nome:'Josué', sigla:'josue', caps:24},
    {nome:'Juízes', sigla:'juizes', caps:21},
    {nome:'Rute', sigla:'rute', caps:4},
    {nome:'1 Samuel', sigla:'1+samuel', caps:31},
    {nome:'2 Samuel', sigla:'2+samuel', caps:24},
    {nome:'1 Reis', sigla:'1+reis', caps:22},
    {nome:'2 Reis', sigla:'2+reis', caps:25},
    {nome:'1 Crônicas', sigla:'1+cronicas', caps:29},
    {nome:'2 Crônicas', sigla:'2+cronicas', caps:36},
    {nome:'Esdras', sigla:'esdras', caps:10},
    {nome:'Neemias', sigla:'neemias', caps:13},
    {nome:'Ester', sigla:'ester', caps:10},
    {nome:'Jó', sigla:'jo', caps:42},
    {nome:'Salmos', sigla:'salmos', caps:150},
    {nome:'Provérbios', sigla:'proverbios', caps:31},
    {nome:'Eclesiastes', sigla:'eclesiastes', caps:12},
    {nome:'Cantares', sigla:'cantico+dos+canticos', caps:8},
    {nome:'Isaías', sigla:'isaias', caps:66},
    {nome:'Jeremias', sigla:'jeremias', caps:52},
    {nome:'Lamentações', sigla:'lamentacoes', caps:5},
    {nome:'Ezequiel', sigla:'ezequiel', caps:48},
    {nome:'Daniel', sigla:'daniel', caps:12},
    {nome:'Oseias', sigla:'oseias', caps:14},
    {nome:'Joel', sigla:'joel', caps:3},
    {nome:'Amós', sigla:'amos', caps:9},
    {nome:'Obadias', sigla:'obadias', caps:1},
    {nome:'Jonas', sigla:'jonas', caps:4},
    {nome:'Miquéias', sigla:'miqueias', caps:7},
    {nome:'Naum', sigla:'naum', caps:3},
    {nome:'Habacuque', sigla:'habacuque', caps:3},
    {nome:'Sofonias', sigla:'sofonias', caps:3},
    {nome:'Ageu', sigla:'ageu', caps:2},
    {nome:'Zacarias', sigla:'zacarias', caps:14},
    {nome:'Malaquias', sigla:'malaquias', caps:4},
    {nome:'Mateus', sigla:'mateus', caps:28},
    {nome:'Marcos', sigla:'marcos', caps:16},
    {nome:'Lucas', sigla:'lucas', caps:24},
    {nome:'João', sigla:'joao', caps:21},
    {nome:'Atos', sigla:'atos', caps:28},
    {nome:'Romanos', sigla:'romanos', caps:16},
    {nome:'1 Coríntios', sigla:'1+corintios', caps:16},
    {nome:'2 Coríntios', sigla:'2+corintios', caps:13},
    {nome:'Gálatas', sigla:'galatas', caps:6},
    {nome:'Efésios', sigla:'efesios', caps:6},
    {nome:'Filipenses', sigla:'filipenses', caps:4},
    {nome:'Colossenses', sigla:'colossenses', caps:4},
    {nome:'1 Tessalonicenses', sigla:'1+tessalonicenses', caps:5},
    {nome:'2 Tessalonicenses', sigla:'2+tessalonicenses', caps:3},
    {nome:'1 Timóteo', sigla:'1+timoteo', caps:6},
    {nome:'2 Timóteo', sigla:'2+timoteo', caps:4},
    {nome:'Tito', sigla:'tito', caps:3},
    {nome:'Filemom', sigla:'filemom', caps:1},
    {nome:'Hebreus', sigla:'hebreus', caps:13},
    {nome:'Tiago', sigla:'tiago', caps:5},
    {nome:'1 Pedro', sigla:'1+pedro', caps:5},
    {nome:'2 Pedro', sigla:'2+pedro', caps:3},
    {nome:'1 João', sigla:'1+joao', caps:5},
    {nome:'2 João', sigla:'2+joao', caps:1},
    {nome:'3 João', sigla:'3+joao', caps:1},
    {nome:'Judas', sigla:'judas', caps:1},
    {nome:'Apocalipse', sigla:'apocalipse', caps:22}
  ];

  var selLivro = document.getElementById('biblia-livro');
  var selCap = document.getElementById('biblia-capitulo');
  var selVers = document.getElementById('biblia-versiculo');
  var btnBuscar = document.getElementById('btnBuscarBiblia');
  var resultado = document.getElementById('biblia-resultado');
  var alert = document.getElementById('biblia-alert');

  // Preenche livros
  livros.forEach(function(l, i){
    var o = document.createElement('option');
    o.value = i;
    o.textContent = l.nome;
    selLivro.appendChild(o);
  });

  function preencherCapitulos(){
    selCap.innerHTML = '';
    var caps = livros[selLivro.value].caps;
    for(var i=1;i<=caps;i++){
      var o = document.createElement('option');
      o.value = i;
      o.textContent = i;
      selCap.appendChild(o);
    }
    preencherVersiculos();
  }

  function preencherVersiculos(){
    selVers.innerHTML = '';
    // Por padrão, mostra 1-50, mas ajusta após buscar
    for(var i=1;i<=50;i++){
      var o = document.createElement('option');
      o.value = i;
      o.textContent = i;
      selVers.appendChild(o);
    }
  }

  selLivro.addEventListener('change', preencherCapitulos);
  selCap.addEventListener('change', preencherVersiculos);

  preencherCapitulos();

  function showAlert(msg, tipo){
    alert.textContent = msg;
    alert.className = 'alert alert-' + (tipo||'danger');
    alert.style.display = msg ? '' : 'none';
  }

  btnBuscar.addEventListener('click', function(){
    var livro = livros[selLivro.value].sigla;
    var cap = selCap.value;
    var vers = selVers.value;
    var buscarCapitulo = !selVers.value || selVers.value === '1';
    var url = buscarCapitulo
      ? 'https://bible-api.com/' + livro + '+' + cap + '?translation=almeida'
      : 'https://bible-api.com/' + livro + '+' + cap + ':' + vers + '?translation=almeida';
    resultado.style.display = 'none';
    showAlert('Buscando...', 'info');
    fetch(url).then(function(r){return r.json();}).then(function(data){
      if(data.error){ showAlert('Não encontrado.', 'danger'); return; }
      showAlert('', '');
      resultado.style.display = '';
      if(buscarCapitulo && data.verses && data.verses.length > 0){
        // Buscar versículos já comentados deste capítulo
        fetch('biblia_comentarios.php?todos_comentados=1&livro='+encodeURIComponent(data.verses[0].book_name)+'&capitulo='+encodeURIComponent(data.verses[0].chapter))
        .then(r=>r.json()).then(function(respComentados){
          var comentados = (respComentados.ok && Array.isArray(respComentados.comentados)) ? respComentados.comentados : [];
          var header = '<div class="card-header"><span class="card-title">' + data.verses[0].book_name + ' ' + data.verses[0].chapter + '</span></div>';
          var body = '<div style="display:flex;flex-direction:column;gap:18px;">';
          data.verses.forEach(function(v){
            var vid = data.verses[0].book_name+"-"+v.chapter+":"+v.verse;
            var destacado = comentados.includes(vid) ? ' biblia-comentado' : '';
            var icone = comentados.includes(vid) ? '🟢' : '📝';
            body += '<div class="biblia-versiculo-card">';
            body +=   '<div class="biblia-versiculo-texto">'
                    + '<span class="biblia-versiculo-num">'+v.verse+'</span>'
                    + v.text.trim()
                    + '<span class="biblia-comentario-icone'+destacado+'" title="Comentar" data-vid="'+vid+'" data-vtexto="'+encodeURIComponent(v.text.trim())+'">'+icone+'</span>'
                    + '</div>';
            body += '</div>';
          });
          body += '</div>';
          resultado.innerHTML = header + body;

          // Atualiza lista de versículos
          if(selVers.options.length !== data.verses.length){
            selVers.innerHTML = '';
            for(var i=1;i<=data.verses.length;i++){
              var o = document.createElement('option');
              o.value = i;
              o.textContent = i;
              selVers.appendChild(o);
            }
          }

          // Handler para abrir modal de comentário
          setTimeout(function(){
            document.querySelectorAll('.biblia-comentario-icone').forEach(function(ic){
              ic.addEventListener('click', function(){
                var vid = ic.getAttribute('data-vid');
                var vtexto = decodeURIComponent(ic.getAttribute('data-vtexto'));
                abrirModalComentario(vid, vtexto, data.verses[0].book_name, data.verses[0].chapter, ic.textContent);
              });
            });
          }, 100);
        });
        return;
      }
        // Função para abrir modal de comentário
        function abrirModalComentario(vid, vtexto, livro, cap, icone) {
          // Remove modal anterior se existir
          var old = document.getElementById('biblia-modal-bg');
          if(old) old.remove();
          // Cria modal
          var bg = document.createElement('div');
          bg.className = 'biblia-modal-bg';
          bg.id = 'biblia-modal-bg';
          bg.innerHTML = '<div class="biblia-modal">'
            + '<button class="biblia-modal-fechar" title="Fechar">&times;</button>'
            + '<div class="biblia-modal-titulo">'+livro+' '+cap+' — Versículo</div>'
            + '<div style="font-size:1.05em;margin-bottom:6px;color:#64748b;">'+vtexto+'</div>'
            + '<textarea class="biblia-comentario" style="width:100%;" rows="4" placeholder="Seu comentário..."></textarea>'
            + '<button class="biblia-comentario-salvar" style="margin-top:10px;padding:7px 18px;font-size:1em;background:#6366f1;color:#fff;border:none;border-radius:6px;cursor:pointer;">Salvar</button>'
            + '<div class="biblia-comentario-status" style="display:none"></div>'
            + '</div>';
          document.body.appendChild(bg);
          var modal = bg.querySelector('.biblia-modal');
          var fechar = bg.querySelector('.biblia-modal-fechar');
          var textarea = bg.querySelector('.biblia-comentario');
          var status = bg.querySelector('.biblia-comentario-status');
          var btnSalvar = bg.querySelector('.biblia-comentario-salvar');
          // Carrega comentário salvo
          fetch('biblia_comentarios.php?versiculo_id='+encodeURIComponent(vid)).then(r=>r.json()).then(function(resp){
            if(resp.ok && resp.comentario) textarea.value = resp.comentario;
          });
          // Salva ao clicar no botão
          btnSalvar.addEventListener('click', function(){
            var val = textarea.value;
            btnSalvar.disabled = true;
            fetch('biblia_comentarios.php', {
              method:'POST',
              headers:{'Content-Type':'application/json'},
              body:JSON.stringify({versiculo_id:vid,comentario:val})
            }).then(r=>r.json()).then(function(resp){
              btnSalvar.disabled = false;
              if(resp.ok){ status.textContent = 'Comentário salvo!'; status.style.display=''; setTimeout(()=>{status.style.display='none';},1500); }
              else { status.textContent = 'Erro ao salvar.'; status.style.display=''; }
            });
          });
          fechar.addEventListener('click', function(){ bg.remove(); });
          bg.addEventListener('click', function(e){ if(e.target===bg) bg.remove(); });
          textarea.focus();
        }
      // Caso padrão: mostra só o versículo
      var versiculo = data.verses && data.verses[0] ? data.verses[0] : null;
      if(!versiculo){ resultado.innerHTML = '<div class="card-body">Versículo não encontrado.</div>'; return; }
      if(selVers.options.length !== data.verses_in_chapter){
        selVers.innerHTML = '';
        for(var i=1;i<=data.verses_in_chapter;i++){
          var o = document.createElement('option');
          o.value = i;
          o.textContent = i;
          selVers.appendChild(o);
        }
        selVers.value = vers;
      }
      resultado.innerHTML = '<div class="card-header"><span class="card-title">' +
        versiculo.book_name + ' ' + versiculo.chapter + ':' + versiculo.verse + '</span></div>' +
        '<div class="card-body" style="font-size:1.2em;line-height:1.6">' + versiculo.text + '</div>';
    }).catch(function(){
      showAlert('Erro ao buscar versículo.', 'danger');
    });
  });
})();
