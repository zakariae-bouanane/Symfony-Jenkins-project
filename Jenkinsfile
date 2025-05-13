pipeline {
    agent any


    environment {
        DOCKERHUB_CREDENTIALS = credentials('dockerHub')
        DOCKER_CONFIG = '/tmp/.docker'
    }



    stages {

        stage('Setup Docker') {
            steps {
                // Make sure Docker and Docker Compose are available
                sh '''
                    docker --version
                    if ! command -v docker-compose &> /dev/null; then
                        echo "Installing Docker Compose ${DOCKER_COMPOSE_VERSION}..."
                        mkdir -p ~/.docker/cli-plugins/
                        curl -SL "https://github.com/docker/compose/releases/download/v${DOCKER_COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o ~/.docker/cli-plugins/docker-compose
                        chmod +x ~/.docker/cli-plugins/docker-compose
                    fi
                    docker compose version
                '''
            }
        }


        stage('Install Dependencies & Clear Cache') {
            steps {
                script {
                    sh 'docker compose build symfony'
                    sh 'docker compose up -d symfony'
                    sh 'docker compose exec -T symfony composer install --no-interaction --no-progress --optimize-autoloader'
                    sh 'docker compose exec symfony php bin/console cache:clear'
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withCredentials([string(credentialsId: 'sonar-token', variable: 'SONAR_TOKEN')]) {
                    sh '''
                        sonar-scanner \
                           -Dsonar.projectKey=symfony-app \
                           -Dsonar.sources=code \
                           -Dsonar.host.url=http://192.168.79.128:9000 \
                           -Dsonar.token=${SONAR_TOKEN}
                    '''
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