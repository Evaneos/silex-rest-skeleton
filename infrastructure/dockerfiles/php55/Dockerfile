FROM rest-api-base:latest

ENV DEBIAN_FRONTEND noninteractive

RUN echo "deb http://packages.dotdeb.org wheezy-php55 all"                >> /etc/apt/sources.list
RUN echo "deb http://packages.dotdeb.org wheezy all"                      >> /etc/apt/sources.list
RUN echo "deb-src http://packages.dotdeb.org wheezy-php55 all"            >> /etc/apt/sources.list
RUN echo "deb http://packages.blackfire.io/debian any main"               >> /etc/apt/sources.list

RUN wget -qO - http://www.dotdeb.org/dotdeb.gpg | apt-key add -
RUN wget -qO - https://packagecloud.io/gpg.key | apt-key add -


RUN apt-get update

RUN apt-get install -y imagemagick
RUN apt-get install -y php5-fpm
RUN apt-get install -y php5-memcached
RUN apt-get install -y php5-memcache
RUN apt-get install -y php5-curl
RUN apt-get install -y php5-gd
RUN apt-get install -y php5-redis
RUN apt-get install -y php5-intl
RUN apt-get install -y php5-pgsql
RUN apt-get install -y php5-mysql
RUN apt-get install -y php5-xdebug
RUN apt-get install -y php5-dev
RUN apt-get install -y php-pear
RUN apt-get install -y libcurl3-dev
RUN apt-get install -y wkhtmltopdf libjpeg8
RUN apt-get install -y blackfire-php

RUN pecl install mongo
RUN echo "extension=mongo.so" > /etc/php5/fpm/conf.d/mongo.ini

RUN sed -i "s/short_open_tag = Off/short_open_tag = On/g" /etc/php5/fpm/php.ini
RUN sed -i "s/short_open_tag = Off/short_open_tag = On/g" /etc/php5/cli/php.ini

RUN sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 15M/g" /etc/php5/fpm/php.ini
RUN sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 15M/g" /etc/php5/cli/php.ini

RUN sed -i "s/post_max_size = 8M/post_max_size = 15M/g" /etc/php5/fpm/php.ini
RUN sed -i "s/post_max_size = 8M/post_max_size = 15M/g" /etc/php5/cli/php.ini

RUN sed -i "s/;date.timezone =/date.timezone = Europe\/Paris/g" /etc/php5/fpm/php.ini
RUN sed -i "s/;date.timezone =/date.timezone = Europe\/Paris/g" /etc/php5/cli/php.ini

EXPOSE 9000

CMD [ "php5-fpm", "-F", "-y", "/config/php-fpm.conf", "-R" ]
