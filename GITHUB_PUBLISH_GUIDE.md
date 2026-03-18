# GitHub Publishing Skills Guide

## Essential Skills to Publish Changes to GitHub

This guide covers **core Git/GitHub skills** needed to add, commit, and push changes successfully. Master these to solve 99% of publishing errors.

### 1. **Git Basics (Must-Know)**
| Skill | Command | Purpose |
|-------|---------|---------|
| Check status | `git status` | See modified/untracked files. **Always run first!** |
| Stage files | `git add <file>` or `git add .` | Prepare files for commit. Use quotes for spaces: `git add "chapter 1/"` |
| Commit | `git commit -m "Message"` | Save snapshot with descriptive message. |
| Push | `git push` | Upload to GitHub. |

**Pro Workflow**:
```
git status
git add .
git commit -m "feat: add PhCalculator"
git push
```

### 2. **GitHub Setup Skills**
| Skill | Command/Step | Purpose |
|-------|--------------|---------|
| Clone repo | `git clone https://github.com/user/repo.git` | Get existing repo. |
| Auth setup | SSH keys or Personal Access Token (PAT) | Avoid password prompts. **See Troubleshooting.** |
| Remote check | `git remote -v` | Verify origin URL. |

### 3. **Troubleshooting Common Errors**
| Error | Cause | Solution |
|--------|-------|----------|
| `fatal: pathspec` | Spaces in path | Use quotes: `git add "chapter 1/"` |
| `Everything up-to-date` | No new changes | Run `git status` – stage/commit first! |
| `not a git repo` | Wrong dir | `git init` or `cd /path/to/repo` |
| `permission denied` | Auth failed | Setup SSH/PAT. |
| `refusing to merge` | Conflicts | `git pull` first, resolve, then push. |
| `detached HEAD` | Wrong branch | `git checkout main` |

**Quick Fix Command**:
```
git status && git add . && git commit -m "update" && git push
```

### 4. **Advanced Skills**
- **Branching**: `git checkout -b feature-branch` → `git push origin feature-branch`
- **Ignore files**: Create `.gitignore` (add `*.jar`, `wget-log*`).
- **Amend commit**: `git commit --amend -m "Better message"`
- **Log history**: `git log --oneline -5`
- **Stash changes**: `git stash` (save uncommitted work).

### 5. **VSCode Integration**
- Install **GitLens** extension.
- Source Control panel (Ctrl+Shift+G): Stage/Commit/Push GUI.
- Terminal: `Ctrl+`` `.

### 6. **GitHub.com Skills**
- Create repo: New → Add README → Clone.
- View commits: Commits tab.
- PRs: New branch → Pull Request.

### 7. **Auth Setup (Critical for Push Errors)**
**Option 1: SSH (Recommended)**:
```
ssh-keygen -t ed25519 -C "email@example.com"
cat ~/.ssh/id_ed25519.pub  # Copy to GitHub SSH keys
ssh -T git@github.com
```

**Option 2: PAT**:
1. GitHub → Settings → Developer → Tokens → Generate.
2. `git remote set-url origin https://TOKEN@github.com/user/repo.git`

### 8. **Practice Checklist**
- [ ] `git status` clean?
- [ ] Files staged? (`git status` green)
- [ ] Good commit message?
- [ ] `git push` succeeds?

**Master Command**: `git add . && git commit -m "changes" && git push`

## Your Recent Success
```
git add "chapter 1/" KOTLIN_VSCODE_SETUP.md ...
git commit -m "Add Kotlin files..."
git push  # → 50315ff on main
```

**Next**: Add `.gitignore` for JARs/logs:
```
echo "*.jar
wget-log*
.DS_Store" > .gitignore
git add .gitignore && git commit -m "add gitignore" && git push
```

Happy Publishing! 🚀
