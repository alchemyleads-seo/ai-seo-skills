#!/usr/bin/env python3
"""Compare every sitemap URL on production vs Webflow staging.
Usage: staging_vs_prod_diff.py https://www.example.com https://example.webflow.io [--paths /a /b]
Reports: prod 'Last Published' stamps, HTTP mismatches, and grouped visible-text / image /
inline-style differences, plus whether the shared site CSS file is identical.
Grouping matters: one shared footer/nav change shows up on every page as the SAME signature."""
import re,sys,html,json,difflib,collections,urllib.request,concurrent.futures as cf
prod,stage=sys.argv[1].rstrip('/'),sys.argv[2].rstrip('/')
UA={'User-Agent':'Mozilla/5.0'}
def get(u):
    try:
        r=urllib.request.urlopen(urllib.request.Request(u,headers=UA),timeout=30);return r.status,r.read().decode('utf8','replace')
    except Exception as e: return getattr(e,'code',0),''
if '--paths' in sys.argv: paths=sys.argv[sys.argv.index('--paths')+1:]
else:
    sm=get(prod+'/sitemap.xml')[1];paths=[re.sub(r'^https?://[^/]+','',u) or '/' for u in re.findall(r'<loc>([^<]+)',sm)]
def text(h):
    h=re.sub(r'(?s)<(script|style|noscript)[^>]*>.*?</\1>','',h);h=re.sub(r'<[^>]+>',' ',h)
    return html.unescape(re.sub(r'\s+',' ',h)).strip().split()
def imgs(h): return set(re.findall(r'(?:src|srcset)="([^"\s]+)',h))
def styles(h): return set(re.findall(r'style="[^"]*"',h))
def stamp(h):
    m=re.search(r'Last Published: ([^-]*?) \(',h);return m and m.group(1)
def one(p):
    ps,ph=get(prod+p);ss,sh=get(stage+p);ops=[]
    a,b=text(ph),text(sh)
    for t,i1,i2,j1,j2 in difflib.SequenceMatcher(None,a,b,autojunk=False).get_opcodes():
        if t!='equal': ops.append(f"TEXT {t}: -[{' '.join(a[i1:i2])[:160]}] +[{' '.join(b[j1:j2])[:160]}]")
    for k,f in (('IMG',imgs),('STYLE',styles)):
        x,y=f(ph),f(sh)
        if x!=y: ops.append(f"{k} -{sorted(x-y)[:4]} +{sorted(y-x)[:4]}")
    return p,ps,ss,stamp(ph),stamp(sh),tuple(ops),ph,sh
with cf.ThreadPoolExecutor(12) as ex: res=list(ex.map(one,paths))
print('pages:',len(res));print('prod Last Published:',{r[3] for r in res});print('stage Last Published:',{r[4] for r in res})
print('status mismatches:',[(r[0],r[1],r[2]) for r in res if r[1]!=200 or r[2]!=200])
css=lambda h:(re.search(r'href="([^"]+\.css)"',h) or [None,None])[1]
cp,cs=css(res[0][6]),css(res[0][7]);print('site CSS identical:',bool(cp and cs) and get(cp)[1]==get(cs)[1])
g=collections.defaultdict(list)
for r in res: g[r[5]].append(r[0])
for k,v in sorted(g.items(),key=lambda x:-len(x[1])):
    print(f'\n{len(v)} pages, e.g. {v[:5]}');[print('   ',o) for o in (k or ('IDENTICAL',))]
