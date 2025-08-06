FROM nginx:latest

COPY conf.d/default.conf /etc/nginx/conf.d/

RUN echo "upstream php-upstream { server api-gateway:9000; }" > /etc/nginx/conf.d/upstream.conf
