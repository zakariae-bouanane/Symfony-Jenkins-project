pipeline {
    agent {
        docker {
            image 'php:8.2-cli'
            args '-v /var/run/docker.sock:/var/run/docker.sock'
        }
    }


    environment {
        DOCKERHUB_CREDENTIALS = credentials('dockerHub')
        DOCKER_CONFIG = '/tmp/.docker'
    }

    stages {
        stage('Install Dependencies & Clear Cache') {
            steps {
                dir('code') {
                    sh '''
                        apt-get update && apt-get install -y git unzip zip curl sudo

                        export COMPOSER_HOME=/tmp/composer

                        curl -sS https://getcomposer.org/installer | php
                        mv composer.phar /usr/local/bin/composer

                        composer install
                        php bin/console cache:clear
                    '''
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withCredentials([string(credentialsId: 'sonar-token', variable: 'SONAR_TOKEN')]) {
                    sh """
                        sonar-scanner \
                           -Dsonar.projectKey=symfony-app \
                           -Dsonar.sources=code \
                           -Dsonar.host.url=http://192.168.79.128:9000 \
                           -Dsonar.token=${SONAR_TOKEN}
                    """
                }
            }
        }

        stage('Deliver') {
            steps {
                sh './scripts/deliver.sh'
            }
        }

        stage('Docker Build') {
            steps {
                script {
                    def name = 'symfony-app'
                    def version = "v1.${BUILD_NUMBER}"

                    sh "docker build -t ${name}:${version} -f infra/php/Dockerfile ."
                }
            }
        }

        stage('Docker Push') {
            steps {
                script {
                    def name = 'symfony-app'
                    def version = "v1.${BUILD_NUMBER}"

                    withCredentials([usernamePassword(credentialsId: 'dockerHub', passwordVariable: 'dockerHubPassword', usernameVariable: 'dockerHubUser')]) {
                        sh """
                            echo ${env.dockerHubPassword} | docker login -u ${env.dockerHubUser} --password-stdin
                            docker tag ${name}:${version} ${env.dockerHubUser}/${name}:${version}
                            docker push ${env.dockerHubUser}/${name}:${version}
                        """
                    }
                }
            }
        }

        stage('Deploy with Ansible') {
            steps {
                sh '''
                    apt-get update && apt-get install -y ansible

                    ansible-playbook -i ansible/inventory.ini ansible/deploy.yml
                '''
            }
        }
    }
}