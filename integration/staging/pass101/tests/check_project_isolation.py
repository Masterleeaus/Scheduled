#!/usr/bin/env python3
"""Exercise the actual launcher through a fake Docker CLI; never starts containers."""
from pathlib import Path
import os, subprocess, tempfile
script = Path(__file__).resolve().parents[1] / 'run.sh'
with tempfile.TemporaryDirectory() as folder:
    base=Path(folder)
    (base/'fixtures').mkdir()
    for name in ('lifecycle.json','concurrency.json'):
        (base/'fixtures'/name).write_text('{}')
    (base/'schema.sql').write_text('SELECT 1;')
    (base/'seed.sql').write_text('SELECT 2;')
    bindir=base/'bin';bindir.mkdir()
    docker=bindir/'docker'
    docker.write_text('#!/bin/sh\nprintf "%s\\n" "$*" >> "$PA103_DOCKER_LOG"\ncase "$*" in *" up -d "*) exit 29;; esac\nexit 0\n')
    docker.chmod(0o755)
    def run(project=None):
        log=base/(('override' if project else 'random')+'.log');log.unlink(missing_ok=True)
        env=os.environ.copy();env.update(PATH=str(bindir)+os.pathsep+env.get('PATH',''),
          PA101_FIXTURES_DIR=str(base/'fixtures'),PA101_SCHEMA_SQL=str(base/'schema.sql'),
          PA101_SEED_SQL=str(base/'seed.sql'),PA101_DB_PASSWORD='test_db_password',
          PA101_ROOT_PASSWORD='test_root_password',PA101_DISPOSABLE_ACK='YES_DESTROY_TEST_DATABASE',
          PA103_DOCKER_LOG=str(log))
        if project is not None:env['PA101_COMPOSE_PROJECT']=project
        else:env.pop('PA101_COMPOSE_PROJECT',None)
        result=subprocess.run(['bash',str(script)],env=env,capture_output=True,text=True)
        return result,log.read_text().splitlines() if log.exists() else []
    result, lines=run()
    assert result.returncode==29,(result.returncode,result.stderr)
    assert len(lines)==3,lines
    tokens=[x.split() for x in lines]
    projects=[x[2] for x in tokens]
    assert len(set(projects))==1 and projects[0].startswith('pa101_'),projects
    assert len(projects[0])==30 and set(projects[0][6:]) <= set('0123456789abcdef'),projects
    assert any('down --volumes --remove-orphans' in x for x in lines),lines
    first=projects[0]
    result2, lines2=run()
    assert result2.returncode==29
    second=lines2[0].split()[2]
    assert first != second,(first,second)
    result3, lines3=run('pa101_fixedunique123')
    assert result3.returncode==29 and all(x.split()[2]=='pa101_fixedunique123' for x in lines3)
    bad, badlines=run('existing-production')
    assert bad.returncode==78 and not badlines
    print('PASS103 launcher: distinct projects, stable cleanup, safe override, fail closed (4/4)')
