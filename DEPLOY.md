# Despliegue de Semillitas del Futuro

Guía para publicar la plataforma (PHP + MySQL) en **Vercel** y recomendaciones
para alojar la base de datos.

---

## 1. Resumen rápido

| Parte | Dónde va | Notas |
|---|---|---|
| Código PHP | Vercel (Docker + FrankenPHP) | `Dockerfile` incluido |
| Base de datos | MySQL externo | Aiven, Railway o TiDB Cloud |
| Fotos / QR / PDFs | MySQL (tabla `archivos`) | `STORAGE_DRIVER=db` |
| Sesiones | MySQL (tabla `sessions`) | `SESSION_DRIVER=database` |

Vercel **no tiene un runtime PHP nativo**, pero desde 2026 acepta un
`Dockerfile` con FrankenPHP y lo ejecuta como *Container Function*. Ese es el
camino que ya está preparado en este proyecto.

> Importante: el sistema de archivos de Vercel es efímero. Por eso las fotos,
> los QR, los materiales y las sesiones se guardan en MySQL, no en disco.

---

## 2. Dónde alojar la base de datos (recomendación)

### Opción recomendada: Aiven for MySQL (plan gratuito)

- 1 GB de disco, 1 CPU, 1 GB de RAM, sin tarjeta de crédito.
- Límite de 76 conexiones simultáneas (suficiente para un colegio).
- Puede apagarse por inactividad; se vuelve a encender desde el panel.
- Requiere TLS: descarga el certificado CA y pégalo en `DB_SSL_CA_PEM`.

Es la mejor relación costo/beneficio para este proyecto.

### Opción más simple: Railway (MySQL)

- Creas un proyecto → *New* → *Database* → *MySQL*.
- Te da `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` listos.
- Plan Hobby desde ~US$ 5/mes (incluye crédito de uso).
- Elige la región `us-east4` (Virginia) para que esté cerca de Vercel `iad1`.

### Opción gratuita alternativa: TiDB Cloud Serverless

- Compatible con MySQL, plan gratuito.
- Usa `DB_PORT=4000`, `DB_SSL=true` y el certificado CA del panel.
- Suficiente para pruebas y volúmenes pequeños.

### Evitar para este proyecto

- **PlanetScale**: ya no tiene plan gratuito y su modo Vitess no respeta
  claves foráneas como MySQL tradicional.
- **Supabase / Neon**: son PostgreSQL. El código usa PDO MySQL; migrar
  requeriría reescribir consultas y el esquema.
- Bases gratuitas que **borran datos** por inactividad o tienen discos muy
  pequeños: no las uses para datos reales del colegio.

> Elige la región de la base de datos lo más cerca posible de tus usuarios
> (por ejemplo, `us-east-1` / Virginia para Perú) y ajusta `regions` en
> `vercel.json` si cambias de región.

---

## 3. Preparar la base de datos

### 3.1 Crear la base y las tablas

En el panel del proveedor crea una base de datos MySQL vacía (por ejemplo
`semillitas_db`). Luego importa el esquema. En la nube usa
`database_cloud.sql`, que **no** incluye `CREATE DATABASE` ni `USE` y por eso
funciona aunque el proveedor asigne otro nombre a la base:

```bash
# Esquema base para la nube (roles, usuarios, materiales, plantas)
mysql -h HOST -P PORT -u USER -p NOMBRE_DE_LA_BD < database_cloud.sql

# Tablas nuevas: archivos, app_data y sessions
mysql -h HOST -P PORT -u USER -p NOMBRE_DE_LA_BD < database_v3.sql

# Columnas de cuidados en plantas (riego, luz)
mysql -h HOST -P PORT -u USER -p NOMBRE_DE_LA_BD < database_v4.sql
```

Si prefieres el script completo de Laragon, `database.sql` también sirve, pero
tendrás que quitar o comentar sus líneas `CREATE DATABASE` y `USE` si el
proveedor no te permite crearlas o usa otro nombre.

En Windows PowerShell la redirección `<` no funciona; usa:

```powershell
cmd /c "mysql -h HOST -P PORT -u USER -p NOMBRE_DE_LA_BD < database_cloud.sql"
cmd /c "mysql -h HOST -P PORT -u USER -p NOMBRE_DE_LA_BD < database_v3.sql"
```

### 3.2 Migrar los datos actuales (recomendado)

Si ya tienes plantas, usuarios y materiales en Laragon:

```bash
mysqldump -u root semillitas_db > respaldo_semillitas.sql
mysql -h HOST -P PORT -u USER -p NOMBRE_DE_LA_BD < respaldo_semillitas.sql
mysql -h HOST -P PORT -u USER -p NOMBRE_DE_LA_BD < database_v3.sql
mysql -h HOST -P PORT -u USER -p NOMBRE_DE_LA_BD < database_v4.sql
```

Las fotos y QR antiguos que estén en disco (`uploads/`) deben migrarse a MySQL
antes de desplegar, porque Vercel no incluye archivos ignorados por Git:

```bash
php tools/migrate_uploads.php
# o, si quieres borrar los archivos locales después de migrarlos:
php tools/migrate_uploads.php --delete
```

El script convierte cada ruta `uploads/...` en `archivo:ID` y sube el contenido
a la base de datos. Ejecútalo apuntando a la base de datos de producción.

---

## 4. Variables de entorno

Configúralas en **Vercel → Project → Settings → Environment Variables**
(o con `vercel env add`).

| Variable | Ejemplo | Obligatoria |
|---|---|---|
| `APP_URL` | `https://semillitas.vercel.app` | Sí |
| `APP_DEBUG` | `false` | Sí |
| `APP_TIMEZONE` | `America/Lima` | No |
| `DB_HOST` | `mysql-xxxx.aivencloud.com` | Sí |
| `DB_PORT` | `3306` (TiDB: `4000`) | Sí |
| `DB_NAME` | `semillitas_db` | Sí |
| `DB_USER` | `avnadmin` | Sí |
| `DB_PASS` | `********` | Sí |
| `DB_SSL` | `true` | Sí (cloud) |
| `DB_SSL_CA_PEM` | contenido del certificado CA | Según proveedor |
| `DB_SSL_VERIFY` | `true` | No |
| `STORAGE_DRIVER` | `db` | Sí |
| `SESSION_DRIVER` | `database` | Sí |
| `SESSION_SECURE_COOKIE` | `true` | Sí |
| `DB_AUTO_MIGRATE` | `true` | No |
| `MAX_FILE_SIZE` | `4000000` | No (Vercel) |

`APP_URL` debe ser el dominio final **antes de generar los QR**, porque los
códigos guardan esa URL. Si cambias de dominio, entra a cada planta y marca
"Regenerar código QR".

> `DB_AUTO_MIGRATE=true` crea las tablas nuevas en el primer arranque. Cuando
> ya existan, puedes ponerlo en `false` para ahorrar consultas.

> Si tu proveedor exige TLS y no cargas el certificado en `DB_SSL_CA_PEM`,
> tendrás que usar `DB_SSL_VERIFY=false`. Es menos seguro; lo ideal es pegar el
> certificado CA completo (con saltos de línea reales) en `DB_SSL_CA_PEM`.

---

## 5. Desplegar en Vercel

### 5.1 Subir el proyecto a GitHub

```bash
git init
git add .
git commit -m "Semillitas listo para producción"
git remote add origin https://github.com/TU-USUARIO/semillitas.git
git push -u origin main
```

### 5.2 Importar en Vercel

1. Entra a <https://vercel.com/new> e importa el repositorio.
2. Vercel detecta el `Dockerfile` y usa el preset **Container**.
3. Agrega todas las variables de entorno de la sección 4.
4. Pulsa **Deploy**.

### 5.3 O desplegar con la CLI

```bash
npm install -g vercel
vercel login
vercel link
vercel env add APP_URL production
vercel env add DB_HOST production
# ... repite para cada variable ...
vercel --prod
```

### 5.4 Comprobar

- Abre `https://TU-DOMINIO/`.
- Inicia sesión con el usuario administrador.
- Crea una planta de prueba: debe guardar foto y generar QR.
- Escanea el QR: debe abrir la ficha y mostrar los juegos recomendados.
- Cierra sesión y vuelve a entrar: la sesión debe sobrevivir a los reinicios.

---

## 6. Límites de Vercel a tener en cuenta

- **Tamaño de petición/respuesta**: alrededor de **4.5 MB** por función.
  - Fotos de celular sin comprimir y PDFs grandes pueden fallar.
  - Por eso `MAX_FILE_SIZE` se puede bajar a `4000000` en Vercel.
  - Si necesitas subir archivos de hasta 15 MB, usa Railway/Render o un
    almacenamiento externo (Vercel Blob / S3 / Cloudflare R2).
- **Arranques en frío**: la primera visita después de un rato inactivo puede
  tardar unos segundos.
- **Sin disco persistente**: resuelto con MySQL (`archivos`, `sessions`).
- **Conexiones a MySQL**: cada petición abre una conexión; el plan gratuito de
  Aiven permite 76 simultáneas. Para un colegio es más que suficiente.

---

## 7. Alternativa recomendada: Railway o Render

Si los archivos grandes son importantes, es más cómodo desplegar todo en un
servicio con contenedores y volumen persistente:

1. Crea un proyecto en Railway/Render.
2. Agrega el repositorio (usa el mismo `Dockerfile`).
3. Crea una base MySQL en el mismo proyecto.
4. Copia las variables de entorno (sin `SESSION_SECURE_COOKIE` si usas HTTP).
5. Opcional: usa `STORAGE_DRIVER=local` y monta un volumen en `/app/uploads`
   y `/app/repositorio/uploads`.
6. Configura el dominio y actualiza `APP_URL`.

En este escenario no hay límite de 4.5 MB y los archivos en disco sobreviven a
los reinicios.

---

## 8. Desarrollo local

```bash
copy .env.example .env
# edita DB_HOST, DB_USER, DB_PASS y APP_URL
```

Con Laragon, abre `http://localhost/semillitas`. El archivo `.env` **no** debe
subirse a GitHub (ya está en `.gitignore`).

Para probar el modo producción localmente:

```bash
docker build -t semillitas .
docker run --rm -p 8080:80 --env-file .env semillitas
```

Luego abre `http://localhost:8080`.

---

## 9. Seguridad

- `APP_DEBUG=false` en producción.
- Usa contraseñas fuertes para MySQL y cámbialas periódicamente.
- Mantén TLS activo (`DB_SSL=true`) al conectar con la base de datos.
- No compartas el archivo `.env` ni el `DB_PASS`.
- Haz respaldos periódicos con `mysqldump` (Aiven y Railway también ofrecen
  respaldos automáticos en sus planes).
- La carpeta `config/`, los `.sql` y `.env` están bloqueados desde Caddy
  (`Caddyfile`), así que no se pueden descargar desde el navegador.
