// Paste into the browser console (javascript_tool) on the target site. Poll window.__fin / window.__cnt / window.__res.
// Edit RE (what to find) and SKIP (expected exceptions such as media URLs and kept logo alts).
window.__res={}; window.__fin=0; window.__cnt=0;
(async()=>{try{
  const RE=/.{0,50}(Term One|Term Two).{0,30}/g;
  const SKIP=/uploads\/|\.(mp4|png|jpg|webp)/;
  const loc=x=>[...x.matchAll(/<loc>(?:<!\[CDATA\[)?([^<\]]+)/g)].map(m=>m[1]);
  const idx=await (await fetch('/sitemap.xml?cb='+Date.now(),{credentials:'omit'})).text();
  let urls=[]; for(const s of loc(idx)){urls.push(...loc(await (await fetch(s,{credentials:'omit'})).text()).filter(u=>!/\.(xml|jpg|png|webp)$/i.test(u)))}
  const q=[...new Set(urls)]; window.__tot=q.length;
  const w=async()=>{while(q.length){const u=q.shift(); try{
    const h=await (await fetch(u+(u.includes('?')?'&':'?')+'cb='+Date.now(),{credentials:'omit',cache:'no-store'})).text(); window.__cnt++;
    for(const m of h.matchAll(RE)){const c=m[0].replace(/\s+/g,' '); if(SKIP.test(c)) continue; (window.__res[c]=window.__res[c]||[]).push(u.replace(location.origin,''))}
  }catch(e){}}};
  await Promise.all([w(),w(),w(),w(),w(),w()]); window.__fin=1;
}catch(e){window.__err=String(e)}})(); 'started';
