FROM docker.elastic.co/elasticsearch/elasticsearch-alpine-base:latest

ADD jvm.options /usr/share/elasticsearch/config/
ADD elasticsearch.yml /usr/share/elasticsearch/config/
USER root
RUN chown elasticsearch:elasticsearch /usr/share/elasticsearch/config/jvm.options /usr/share/elasticsearch/config/elasticsearch.yml

USER elasticsearch
EXPOSE 9200 9300