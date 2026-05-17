# ACDB1_Bryan-Ortu-o
# Sistema de Autenticación y Perfil de Usuario - UTPL

Este es un sistema web básico desarrollado en PHP y MySQL que implementa autenticación segura, manejo de sesiones y actualización de datos de perfil siguiendo buenas prácticas de ciberseguridad.

## 🛠️ Requisitos del Sistema
* **Servidor Local:** XAMPP v3.3.0 o superior.
* **PHP:** Versión 8.2 o superior.
* **Base de Datos:** MySQL PHPMYADMIN.
* **Framework Frontend:** Bootstrap 5.3 (vía CDN).

## 🚀 Pasos para la Instalación Local

1. **Clonar o descargar el proyecto:**
   Descarga este repositorio y coloca la carpeta dentro del directorio de tu servidor local (usualmente `C:\xampp\htdocs\ACDB1\`).

2. **Configurar la Base de Datos:**
   * Abre **phpMyAdmin** (`http://localhost/phpmyadmin/`).
   * Crea una nueva base de datos llamada `sistema_usuarios`.
   * Ejecuta el siguiente script SQL para crear la tabla de usuarios obligatoria:

   ```sql
   CREATE TABLE usuarios (
       cedula VARCHAR(10) PRIMARY KEY,
       nombre VARCHAR(100) NOT NULL,
       correo VARCHAR(100) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;