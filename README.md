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

# Endpoint 5: @POST - Buscar Token: Authorization, Basic Auth @parms: username=CLIENT_ID, password=CLIENT_SECRET Enviar via x-www-form-urlencode @param grant_type=client_credentials, https://webservice-slim.herokuapp.com/oauth2/token

#Retorna um json no formato:
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpZCI6IjAzYjgzOTQ4ZWIxZjhkZDhlN2E5MGQ0NmQzMDI3NDA3MzAzOWM1NjgiLCJqdGkiOiIwM2I4Mzk0OGViMWY4ZGQ4ZTdhOTBkNDZkMzAyNzQwNzMwMzljNTY4IiwiaXNzIjoiIiwiYXVkIjoiQ0xJRU5UX0lEIiwic3ViIjpudWxsLCJleHAiOjE1MzAyMzQxNTUsImlhdCI6MTUzMDIzMDU1NSwidG9rZW5fdHlwZSI6ImJlYXJlciIsInNjb3BlIjpudWxsfQ.jrYevkfIGP2jOrRTKbLptstpyALQes1W_rQmW0_4xmv35d13VMtKIApWETL9yNJecm-wNdmvV1YhFd8SefoDwrrJl05-3hmViRJWQRKC1mTOzqPD_u8luFUKYSFpCY_UCbxsm3iC4hWJVcoVXHHpTNYd18BW8QNLstskGE8K3BA",
    "expires_in": 3600,
    "token_type": "bearer",
    "scope": null
}

# Endpoint 6: @GET - Validar Token: Authorization, Bearer {token}, https://webservice-slim.herokuapp.com/oauth2/teste-token
#Retorna um json no formato:
{
    "status": 1,
    "response": "Autenticado, token válido recurso aceito para consumo da api",
    "data": []
}
