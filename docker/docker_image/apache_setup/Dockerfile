FROM httpd:2.4

# Copy custom Apache config
COPY my-site.conf /usr/local/apache2/conf/extra/my-site.conf
RUN echo "Include conf/extra/my-site.conf" >> /usr/local/apache2/conf/httpd.conf

# Copy site files
COPY html/ /usr/local/apache2/htdocs/
