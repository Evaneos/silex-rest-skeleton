FROM rest-api-base:latest

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get install -y nginx

EXPOSE 80

CMD [ "nginx", "-c", "/config/nginx.conf" ]