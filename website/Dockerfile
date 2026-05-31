FROM nginx:alpine
COPY . /usr/share/nginx/html

# Create nginx config template with a placeholder for PORT
RUN cat > /etc/nginx/conf.d/default.conf << 'NGINX_CONF'
server {
    listen __PORT__;
    location / {
        root /usr/share/nginx/html;
        index index.html;
    }
}
NGINX_CONF

# At runtime: replace __PORT__ with Railway's PORT variable, then start nginx
CMD sed -i "s/__PORT__/${PORT:-80}/" /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;'
