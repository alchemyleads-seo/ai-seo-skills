// Browser console / javascript_tool on the live site (same origin). Poll window.__tp.done, then print window.__tpReport().
// Logged-out fetches of every sitemap URL; static HTML only (GTM-injected tags need a network-level check).
window.__tp={hosts:{},vendors:{},done:0,n:0,err:[]};
(async()=>{try{
  const FP={gtag:/gtag\(|googletagmanager\.com\/gtag/,GTM:/googletagmanager\.com\/gtm\.js|GTM-[A-Z0-9]+/,MetaPixel:/connect\.facebook\.net|fbq\(/,HubSpot:/hs-scripts\.com|hsforms|hs-analytics/,PostHog:/posthog/i,Hotjar:/hotjar/i,Clarity:/clarity\.ms/,LinkedIn:/snap\.licdn\.com|_linkedin_partner_id/,TikTok:/analytics\.tiktok\.com|ttq\./,Calendly:/calendly\.com/,YouTube:/youtube(-nocookie)?\.com\/embed/,Vimeo:/player\.vimeo\.com/,GoogleFonts:/fonts\.googleapis\.com/,reCAPTCHA:/recaptcha/,Cloudflare:/cloudflareinsights|challenges\.cloudflare/};
  const loc=x=>[...x.matchAll(/<loc>(?:<!\[CDATA\[)?([^<\]]+)/g)].map(m=>m[1]);
  const idx=await (await fetch('/sitemap.xml?cb='+Date.now(),{credentials:'omit'})).text();
  let urls=[]; for(const s of loc(idx)) urls.push(...loc(await (await fetch(s,{credentials:'omit'})).text()).filter(u=>!/\.(xml|jpg|png|webp)$/i.test(u)));
  const q=[...new Set(urls)]; window.__tp.total=q.length; const own=location.hostname.replace(/^www\./,'');
  const w=async()=>{while(q.length){const u=q.shift(); try{
    const h=await (await fetch(u,{credentials:'omit',cache:'no-store'})).text(); window.__tp.n++;
    const d=new DOMParser().parseFromString(h,'text/html'); const seen=new Set();
    d.querySelectorAll('script[src],iframe[src],img[src],link[href]').forEach(e=>{const v=e.getAttribute('src')||e.getAttribute('href'); try{const x=new URL(v,u); if(!x.hostname.endsWith(own)&&/^https?:/.test(x.protocol)) seen.add(e.tagName.toLowerCase()+':'+x.hostname)}catch(_){}});
    seen.forEach(k=>{(window.__tp.hosts[k]=window.__tp.hosts[k]||new Set()).add(u)});
    for(const [v,re] of Object.entries(FP)) if(re.test(h)) (window.__tp.vendors[v]=window.__tp.vendors[v]||new Set()).add(u);
  }catch(e){window.__tp.err.push(u+': '+e)}}};
  await Promise.all([w(),w(),w(),w(),w(),w()]); window.__tp.done=1;
}catch(e){window.__tp.err.push(String(e)); window.__tp.done=1}})();
window.__tpReport=()=>({routes:window.__tp.n+'/'+window.__tp.total, errors:window.__tp.err.length,
  vendors:Object.fromEntries(Object.entries(window.__tp.vendors).map(([k,s])=>[k,s.size])),
  hosts:Object.entries(window.__tp.hosts).map(([k,s])=>[k,s.size]).sort((a,b)=>b[1]-a[1])});
'started';
