import os, re, sys
root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
locs = ['uz','ru','en','kk','ky','kaa']
def load(fn):
    rows = {}
    for ln, line in enumerate(open(fn, encoding='utf-8'), 1):
        line = line.rstrip('\n')
        if not line.strip(): continue
        p = line.split('|')
        key = p[0]
        vals = p[1:7] + [''] * (6 - len(p[1:7]))
        if len(p) > 7: print('WARN extra | in', fn, ln, key)
        rows[key] = vals
    return rows
def esc(s): return "'" + s.replace('\\','\\\\').replace("'", "\\'") + "'"
for name in ['ui', 'admin']:
    rows = load(os.path.join(os.path.dirname(os.path.abspath(__file__)), name + '.tsv'))
    for i, loc in enumerate(locs):
        os.makedirs(os.path.join(root, 'lang', loc), exist_ok=True)
        out = ['<?php', '', 'return [']
        for k, v in rows.items():
            if v[i].strip():
                out.append(f"    {esc(k)} => {esc(v[i])},")
        out.append('];'); out.append('')
        open(os.path.join(root, 'lang', loc, name + '.php'), 'w', encoding='utf-8').write('\n'.join(out))
print('ok')
