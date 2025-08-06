FROM nginx:latest

COPY conf.d/default.conf /etc/nginx/conf.d/

RUN echo "upstream php-upstream { server user-service:9000; }" > /etc/nginx/conf.d/upstream.conf
