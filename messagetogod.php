<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Welcome to Big Messaging</title>
<link rel="stylesheet" href="style2.css">
</head>
<body>
<h1>Welcome To Big Messaging</h1>
<p class="sub">Talk about Skibidi, Phonk, And Habsburgs.</p>

<div class="room">
  <div id="log"><p class="empty">Loading messages…</p></div>
  <div class="row">
    <input id="text" class="inputtext" maxlength="255" placeholder="Type a message…" autocomplete="off">
  </div>
  <button id="send" type="button">Send</button>
  <p id="status"></p>
</div>

<script>
(function(){
  var log=document.getElementById('log'), textEl=document.getElementById('text'),
      sendBtn=document.getElementById('send'),
      status=document.getElementById('status');

  function render(msgs){
    var atBottom=log.scrollHeight-log.scrollTop-log.clientHeight<60;
    log.textContent='';
    if(!msgs.length){var p=document.createElement('p');p.className='empty';p.textContent='Type blad';log.appendChild(p);return;}
    msgs.forEach(function(m){
      var d=document.createElement('div'); d.className=m.is_me?'msg me':'msg';
      var meta=document.createElement('div'); meta.className='msg-meta';
      var w=document.createElement('span'); w.className='who'; w.textContent=m.name||'Anonymous';
      var t=document.createElement('span'); t.textContent=m.text||'';
      var time=document.createElement('time'); time.className='message-time'; time.textContent=m.time||'';
      meta.appendChild(w);
      if(m.time) meta.appendChild(time);
      d.appendChild(meta); d.appendChild(t); log.appendChild(d);
    });
    if(atBottom) log.scrollTop=log.scrollHeight;
  }

  async function send(){
    var text=textEl.value.trim(); if(!text) return;
    var body=new URLSearchParams({message:text});
    sendBtn.disabled=true;
    try{
      var response=await fetch('messageservice.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body.toString()});
      var result=await response.json();
      if(!response.ok) throw new Error(result.error||'Could not send that message.');
      textEl.value='';
      status.textContent='';
      await loadMessages();
      textEl.focus();
    }catch(e){status.textContent=e.message||'Could not send that message. Please try again.';}
    finally{sendBtn.disabled=false;}
  }
  sendBtn.addEventListener('click',send);
  textEl.addEventListener('keydown',function(e){ if(e.key==='Enter'){e.preventDefault();send();} });

  async function loadMessages(){
    try{
      var response=await fetch('messageservice.php',{cache:'no-store'});
      var messages=await response.json();
      if(!response.ok) throw new Error(messages.error||'Could not load messages.');
      render(messages);
      if(status.textContent==='Could not load messages. Check the server connection.'){status.textContent='';}
    }catch(e){status.textContent='Could not load messages. Check the server connection.';}
  }
  render([]);
  loadMessages();
  window.setInterval(loadMessages,1000);
})();
</script>
</body>
</html>