import os
import re
from builder_helper import page_layout

GUIDE_DIR = "/home/niduka/iwt/scholarship_system/GUIDE"
SECTIONS_DIR = os.path.join(GUIDE_DIR, "sections")

FILE_SUBNAV = [
    ("file-schema-sql", "schema.sql (DB Schema & Seed)"),
    ("file-config-db", "config/db.php (PDO Connection)"),
    ("file-index-php", "index.php (Entrypoint Redirect)"),
    ("file-public-logout", "public/logout.php (Session Teardown)"),
    ("file-public-admin", "public/admin.php (Admin Control Panel)"),
    ("file-public-login", "public/login.php (Authentication)"),
    ("file-public-signup", "public/signup.php (Registration)"),
    ("file-public-applications", "public/applications.php (Registrar Panel)"),
    ("file-public-home", "public/home.php (Student Portal)"),
    ("file-includes-header", "includes/header.php (Global Header)"),
    ("file-includes-footer", "includes/footer.php (Global Footer)"),
    ("file-public-functionalities", "public/functionalities.php (System Features)"),
    ("file-public-help", "public/help.php (Help & FAQs)"),
    ("file-public-css-style", "public/css/style.css (UI Stylesheet)"),
]

def make_sticky_nav():
    items = [
        ("file-schema-sql", "schema.sql", "SQL", "tech-sql", "61 LOC"),
        ("file-config-db", "config/db.php", "PHP", "tech-php", "15 LOC"),
        ("file-index-php", "index.php", "PHP", "tech-php", "4 LOC"),
        ("file-public-logout", "public/logout.php", "PHP", "tech-php", "8 LOC"),
        ("file-public-admin", "public/admin.php", "PHP/JS", "tech-php", "280 LOC"),
        ("file-public-login", "public/login.php", "PHP/JS", "tech-php", "84 LOC"),
        ("file-public-signup", "public/signup.php", "PHP/JS", "tech-php", "96 LOC"),
        ("file-public-applications", "public/applications.php", "PHP/JS", "tech-php", "221 LOC"),
        ("file-public-home", "public/home.php", "PHP/JS/HTML", "tech-php", "361 LOC"),
        ("file-includes-header", "includes/header.php", "PHP/HTML", "tech-php", "38 LOC"),
        ("file-includes-footer", "includes/footer.php", "HTML", "tech-html", "3 LOC"),
        ("file-public-functionalities", "public/functionalities.php", "HTML/PHP", "tech-html", "52 LOC"),
        ("file-public-help", "public/help.php", "HTML/PHP", "tech-html", "50 LOC"),
        ("file-public-css-style", "public/css/style.css", "CSS", "tech-css", "299 LOC"),
    ]
    pills = "".join([
        f'<a href="#{anchor}" class="pill"><span class="tech-badge {badge_cls}">{lang}</span> <strong>{name}</strong> <span class="loc-badge">{loc}</span></a>\n'
        for anchor, name, lang, badge_cls, loc in items
    ])
    return f"""<div class="file-nav-sticky">
    <div style="font-size:0.85rem; font-weight:700; color:var(--text-muted); margin-bottom: 6px; width: 100%;">⚡ QUICK JUMP TO REPOSITORY FILE (14 OF 14 FILES DECONSTRUCTED):</div>
    {pills}
</div>"""

def assemble_codebase():
    # Read the 3 section files
    p1_path = os.path.join(SECTIONS_DIR, "part1_backend_db.html")
    p2_path = os.path.join(SECTIONS_DIR, "part2_portal_auth.html")
    p3_path = os.path.join(SECTIONS_DIR, "part3_frontend_css.html")

    with open(p1_path, "r", encoding="utf-8") as f:
        p1 = f.read().strip()
    with open(p2_path, "r", encoding="utf-8") as f:
        p2 = f.read().strip()
    with open(p3_path, "r", encoding="utf-8") as f:
        p3 = f.read().strip()

    hero_html = """
<div class="hero-banner">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1>💻 Complete Codebase Deconstruction</h1>
            <p>An exhaustive, line-by-line pedagogical breakdown of <strong>every single file</strong> in the <strong>Scholarship Management System</strong> repository. Zero lines omitted, 100% contiguous code blocks, direct mapping to the UCSC IS1207 syllabus, past examination traps, examiner viva questions, and hands-on live coding challenges.</p>
        </div>
        <div style="text-align: right;">
            <span class="badge" style="font-size: 0.9rem; padding: 6px 14px;">14 / 14 Files Deconstructed</span>
            <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 6px;">1,572 Lines of Production Code</div>
        </div>
    </div>
</div>

<div class="box box-concept">
    <div class="box-header">
        <span class="box-tag">Codebase Architecture &amp; Examination Blueprint</span>
        <span class="file-badge">14 Files &bull; Zero Lines Skipped &bull; IS1207 Calibrated</span>
    </div>
    <p>This reference section is built for two high-stakes academic purposes:</p>
    <ul>
        <li><strong>For the Viva Defense Session:</strong> Gain mastery over every architectural decision, session lifecycle, prepared statement, relational join, and DOM Level 0 validation routine. Be fully prepared to defend the code line-by-line against faculty questions and solve on-the-spot live modifications.</li>
        <li><strong>For the IS1207 Final Semester Examination:</strong> Deepen your understanding of syllabus-compliant syntax, DOM Level 0 form hierarchies, SQL composite keys, cascading deletes, float clearfixes, and PHP type juggling traps tested in semester past papers.</li>
    </ul>
</div>
"""

    main_content = hero_html + "\n" + make_sticky_nav() + "\n\n"
    main_content += "<!-- ================= PART 1: DATABASE, BACKEND & ADMINISTRATION ================= -->\n"
    main_content += p1 + "\n\n"
    main_content += "<hr style=\"border-color: var(--border-color); margin: 60px 0;\">\n\n"
    main_content += "<!-- ================= PART 2: APPLICATION PORTAL & CORE WORKFLOWS ================= -->\n"
    main_content += p2 + "\n\n"
    main_content += "<hr style=\"border-color: var(--border-color); margin: 60px 0;\">\n\n"
    main_content += "<!-- ================= PART 3: FRONTEND TEMPLATES, CONTENT & CSS ================= -->\n"
    main_content += p3 + "\n\n"

    full_html = page_layout(
        title="Complete Codebase Deconstruction",
        active_id="codebase",
        subnav_items=FILE_SUBNAV,
        content=main_content
    )

    out_path = os.path.join(GUIDE_DIR, "codebase.html")
    with open(out_path, "w", encoding="utf-8") as f:
        f.write(full_html)
    
    print(f"Successfully generated {out_path} ({len(full_html):,} bytes)")

def update_all_sidebars():
    pages = ["index.html", "html.html", "css.html", "js.html", "php.html", "sql.html", "viva.html"]
    for p in pages:
        fpath = os.path.join(GUIDE_DIR, p)
        if not os.path.exists(fpath):
            continue
        with open(fpath, "r", encoding="utf-8") as f:
            content = f.read()
        
        if 'href="codebase.html"' not in content:
            viva_pattern = r'(<a href="viva\.html" class="nav-item [^"]*">🎓 6\. Viva Defense & Q&A</a>)'
            replacement = r'\1\n<a href="codebase.html" class="nav-item ">💻 7. Codebase Walkthrough</a>'
            new_content = re.sub(viva_pattern, replacement, content)
            if new_content != content:
                with open(fpath, "w", encoding="utf-8") as f:
                    f.write(new_content)
                print(f"Updated sidebar in {p}")

def update_index_curriculum():
    index_path = os.path.join(GUIDE_DIR, "index.html")
    with open(index_path, "r", encoding="utf-8") as f:
        content = f.read()
    
    if "7. Complete Codebase Walkthrough" not in content and "7. Codebase Walkthrough" not in content:
        card_html = """
    <div class="box box-codebase">
        <h3 class="sub-title"><a href="codebase.html">💻 7. Complete Codebase Walkthrough (14 Files) &rarr;</a></h3>
        <ol style="margin-left: 18px; font-size: 0.9rem;">
            <li>schema.sql: DDL, Composite Keys, Referential Integrity &amp; Cascades</li>
            <li>config/db.php: PDO DSN, Exception Modes &amp; Connection Security</li>
            <li>index.php &amp; logout.php: HTTP 302 Redirection &amp; Session Teardown</li>
            <li>includes/header.php &amp; footer.php: Responsive Nav &amp; Viewport Meta</li>
            <li>public/login.php &amp; signup.php: Auth Pipeline &amp; DOM Level 0 Validation</li>
            <li>public/home.php: Student Dashboard, Upsert Logic &amp; 12-Digit NIC Check</li>
            <li>public/applications.php: Registrar Catalog CRUD &amp; Status Transitions</li>
            <li>public/admin.php: User Management CRUD &amp; 4-Table Overview Join</li>
            <li>public/functionalities.php &amp; help.php: Semantic Roles &amp; Documentation</li>
            <li>public/css/style.css: Float Layouts, Card Grids, Gradients &amp; Hover States</li>
        </ol>
    </div>
</div>"""
        content = content.replace("</div>\n</div>\n\n    </main>", card_html + "\n\n    </main>")
        with open(index_path, "w", encoding="utf-8") as f:
            f.write(content)
        print("Updated index.html with 7th curriculum box.")

if __name__ == "__main__":
    update_all_sidebars()
    update_index_curriculum()
    assemble_codebase()
