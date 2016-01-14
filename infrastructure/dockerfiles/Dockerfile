FROM debian:wheezy

MAINTAINER Olivier Madre <terenas@gmail.com>

ENV DEBIAN_FRONTEND noninteractive

RUN echo "deb http://ftp.fr.debian.org/debian/ wheezy main"         >  /etc/apt/sources.list
RUN echo "deb http://ftp.fr.debian.org/debian/ wheezy-updates main" >> /etc/apt/sources.list
RUN echo "deb http://security.debian.org wheezy/updates main"       >> /etc/apt/sources.list

RUN apt-get update
RUN apt-get install -y wget
RUN apt-get install -y vim
RUN apt-get install -y telnet
RUN apt-get install -y curl