#!/usr/bin/env python3
from pathlib import Path
import hashlib,json,zipfile
ROOT=Path(__file__).resolve().parents[1]
OUT=ROOT/'dist';OUT.mkdir(exist_ok=True)
archive=OUT/'kvkk-denetci-cpanel.zip'
entries={}
def add(path,target):
    if path.is_file():entries[target]=path.read_bytes()
for name in ['index.php','api.php','install.php','.htaccess','.user.ini','robots.txt']:
    add(ROOT/name,'public_html/'+name)
for file in sorted((ROOT/'assets').rglob('*')):
    if file.is_file():add(file,'public_html/'+str(file.relative_to(ROOT)))
for file in sorted((ROOT/'app').rglob('*')):
    if file.is_file():add(file,'kvkk-private/'+str(file.relative_to(ROOT)))
add(ROOT/'bin/maintenance.php','kvkk-private/bin/maintenance.php')
add(ROOT/'.env.production.example','kvkk-private/.env.example')
add(ROOT/'storage/.htaccess','kvkk-private/storage/.htaccess')
entries['kvkk-private/storage/.gitkeep']=b''
add(ROOT/'belgeler/cpanel-kurulum.md','KURULUM.md')
manifest={name:hashlib.sha256(data).hexdigest() for name,data in entries.items()}
entries['MANIFEST.json']=json.dumps(manifest,ensure_ascii=False,indent=2).encode()
with zipfile.ZipFile(archive,'w',zipfile.ZIP_DEFLATED) as z:
    for name,data in sorted(entries.items()):z.writestr(name,data)
print(f'{archive}\n{len(entries)} dosya; yerel veritabanı ve sırlar dahil edilmedi.')
