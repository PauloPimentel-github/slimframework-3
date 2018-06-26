# Webservice para consumo de aplicações para dispositivos móveis ou web
#Webservice implementado no padrão restFull, tecnologias como PHP, MySQL, OAuht 2.0

# Endpoint 1: @GET - Listar Categorias e dados do Site: https://webservice-slim.herokuapp.com/data-site
#Retorna um json contendo o status, resposta e um array de categorias e dados do site

# Endpoint 2: @GET - Buscar dados de um CEP válido, recebe uma string como parâmetro - Buscar Cep: https://webservice-slim.herokuapp.com/cep=07263725
#Retorna um json contendo o status, resposta e um obj data com as informações do cep que foi passado como argumento

# Endpoint 3: @GET - Buscar categoria por id:  https://webservice-slim.herokuapp.com/categoria=1
#Retorna um json contendo o status, resposta e data com informações da tabela de categoria relacionada a tabela de artigo pelo id

# Endpoint 4: @POST - Cadastrar usuário: @params: Objeto User, atributos @string name, @int idade, @string cpf: https://webservice-slim.herokuapp.com/user
#Retorna um json contendo o status, resposta e data com informações do objeto cadastrado na requisição
