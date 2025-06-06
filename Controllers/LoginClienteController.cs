using Microsoft.AspNetCore.Mvc;
using MySql.Data.MySqlClient;
using BCrypt.Net;
using Microsoft.Extensions.Configuration;

namespace GymvaApi.Controllers
{
    [ApiController]
    [Route("api/login")]
    public class LoginClienteController : ControllerBase
    {
        private readonly IConfiguration _config;

        public LoginClienteController(IConfiguration config) => _config = config;

        public class LoginRequest
        {
            public string Email { get; set; }
            public string Contrasena { get; set; }
        }

        [HttpPost]
        public IActionResult Post([FromBody] LoginRequest request)
        {
            var connStr = _config.GetConnectionString("DefaultConnection");

            using var conn = new MySqlConnection(connStr);
            conn.Open();

            var cmd = new MySqlCommand("SELECT contrasena_cliente FROM cliente WHERE email_cliente = @Email", conn);
            cmd.Parameters.AddWithValue("@Email", request.Email);

            var result = cmd.ExecuteScalar();

            if (result == null)
                return Unauthorized(new { success = false, message = "Email no encontrado" });

            var hashAlmacenado = result.ToString();

            if (!BCrypt.Net.BCrypt.Verify(request.Contrasena, hashAlmacenado))
                return Unauthorized(new { success = false, message = "Contraseña incorrecta" });

            return Ok(new { success = true, message = "Login correcto" });
        }
    }
}
