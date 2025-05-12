#!/usr/bin/env bash

echo 'The following Maven command installs your Maven-built Java application'
echo 'into the local Maven repository, which will ultimately be stored in'
echo 'Jenkins''s local Maven repository (and the "maven-repository" Docker data'
echo 'volume).'
set -x
mvn jar:jar install:install help:evaluate -Dexpression=project.name
set +x

echo 'The following command extracts the value of the <name/> element'
echo 'within <project/> of your Java/Maven project''s "pom.xml" file.'
set -x
NAME=`mvn -q -DforceStdout help:evaluate -Dexpression=project.name`
set +x

echo 'The following command behaves similarly to the previous one but'
echo 'extracts the value of the <version/> element within <project/> instead.'
set -x
VERSION=`mvn -q -DforceStdout help:evaluate -Dexpression=project.version`
set +x

# echo 'Building the Docker image.'
# set -x
# docker build -t ${NAME}:${VERSION} . 
# set +x

# echo 'Tagging the Docker image for DockerHub.'
# DOCKER_USERNAME=your_dockerhub_username
# set -x
# docker tag ${NAME}:${VERSION} ${DOCKER_USERNAME}/${NAME}:${VERSION}
# set +x

# echo 'Pushing the Docker image to DockerHub.'
# set -x
# docker push ${DOCKER_USERNAME}/${NAME}:${VERSION}
# set +x

# echo 'Docker image ${DOCKER_USERNAME}/${NAME}:${VERSION} has been successfully pushed to DockerHub.'


echo 'The following command runs and outputs the execution of your Java'
echo 'application (which Jenkins built using Maven) to the Jenkins UI.'
set -x
java -jar target/${NAME}-${VERSION}.jar
set +x