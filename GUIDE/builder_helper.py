import os

def page_layout(title, active_id, subnav_items, content):
    nav_links = [
        ("index.html", "index", "🏠 Home & Overview"),
        ("html.html", "html", "🌐 1. HTML Mastery"),
        ("css.html", "css", "🎨 2. CSS & Layouts"),
        ("js.html", "js", "⚡ 3. JavaScript & DOM"),
        ("php.html", "php", "🐘 4. PHP & Backend"),
        ("sql.html", "sql", "🗄️ 5. SQL & Relational DB"),
        ("viva.html", "viva", "🎓 6. Viva Defense & Q&A"),
    ]
    
    sidebar_nav_html = ""
    for href, page_id, label in nav_links:
        is_active = "active" if page_id == active_id else ""
        sidebar_nav_html += f'<a href="{href}" class="nav-item {is_active}">{label}</a>\n'
        if page_id == active_id and subnav_items:
            sidebar_nav_html += '<div class="sub-nav">\n'
            for anchor, item_label in subnav_items:
                sidebar_nav_html += f'  <a href="#{anchor}">{item_label}</a>\n'
            sidebar_nav_html += '</div>\n'
            
    return f"""<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{title} | IS1207 Complete Study Guide</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>IS1207 Guide</h2>
            <span class="badge">Syllabus + Past Papers + Codebase</span>
        </div>
        <nav class="sidebar-nav">
            {sidebar_nav_html}
        </nav>
    </aside>
    <main class="main-wrapper">
        {content}
    </main>
</body>
</html>
"""
