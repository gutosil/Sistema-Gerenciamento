# Gerenciamento — React

Este frontend React mantém as telas e a identidade visual do `index.html` original. Os registros são carregados e salvos pela API Laravel em `/api`.

```powershell
npm.cmd install
npm.cmd run dev:all
```

O comando `dev:all` inicia a API Laravel e o Vite juntos, mesmo quando executado dentro de `frontend`. No PowerShell do Windows, use `npm.cmd` para evitar o bloqueio de scripts do `npm.ps1`. O Vite encaminha `/api` para `http://127.0.0.1:8000`. Consulte o [README principal](../README.md) para preparar o banco e executar os testes.
