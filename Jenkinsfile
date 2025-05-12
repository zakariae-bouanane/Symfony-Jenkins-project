pipeline {
    agent {
        docker {
            image 'php:8.2-cli'
            args '-v /var/run/docker.sock:/var/run/docker.sock'
        }
    }

    triggers {
        cron('0 * * * *') // Runs every hour
    }

    environment {
        DOCKERHUB_CREDENTIALS = credentials('dockerHub')
        DOCKER_CONFIG = '/tmp/.docker'
    }

    stages {
        stage('Install Dependencies & Clear Cache') {
            steps {
                sh '''
                    # Install necessary dependencies (git, unzip, etc.)
                    apt-get update && apt-get install -y git unzip zip curl

                    # Install Composer
                    curl -sS https://getcomposer.org/installer | php
                    php composer.phar install --working-dir=code

                    # Clear Symfony cache
                    php code/bin/console cache:clear
                '''
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withCredentials([string(credentialsId: 'sonar-token', variable: 'SONAR_TOKEN')]) {
                    sh """
                        # Run SonarQube analysis in the 'code' directory
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
                # Replace this with your actual deliver script
                sh './jenkins/scripts/deliver.sh'
            }
        }

        stage('Docker Build') {
            steps {
                script {
                    def name = 'symfony-app'
                    def version = "v1.${BUILD_NUMBER}"

                    # Build Docker image with context from the root directory
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
                            # Login to DockerHub and push the image
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
                    # Install Ansible
                    apt-get update && apt-get install -y ansible

                    # Run Ansible playbook for deployment
                    ansible-playbook -i ansible/inventory.ini ansible/deploy.yml
                '''
            }
        }
    }
}