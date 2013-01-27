html :
	pandoc -5 -s -S -c bootstrap/css/bootstrap.min.css -c docs.css -B html/header.html -A html/footer.html  -f markdown -t html -o html/index.html index.md
	pandoc -5 -s -S -c bootstrap/css/bootstrap.min.css -c docs.css -B html/header.html -A html/footer.html  -f markdown -t html -o html/sessions.html sessions.md
	pandoc -5 -s -S -c bootstrap/css/bootstrap.min.css -c docs.css -B html/header.html -A html/footer.html  -f markdown -t html -o html/posts.html posts.md
	pandoc -5 -s -S -c bootstrap/css/bootstrap.min.css -c docs.css -B html/header.html -A html/footer.html  -f markdown -t html -o html/users.html users.md
	pandoc -5 -s -S -c bootstrap/css/bootstrap.min.css -c docs.css -B html/header.html -A html/footer.html  -f markdown -t html -o html/stream.html stream.md
word :
	pandoc -f markdown -t docx index.md sessions.md
pdf :
	markdown2pdf index.md sessions.md posts.md users.md stream.md -o doc.pdf --toc
