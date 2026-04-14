FROM httpd:alpine

RUN sed -i \
    's/#LoadModule proxy_module/LoadModule proxy_module/' /usr/local/apache2/conf/httpd.conf && \
    sed -i \
    's/#LoadModule proxy_fcgi_module/LoadModule proxy_fcgi_module/' /usr/local/apache2/conf/httpd.conf && \
    echo "Include conf/extra/vhost.conf" >> /usr/local/apache2/conf/httpd.conf
