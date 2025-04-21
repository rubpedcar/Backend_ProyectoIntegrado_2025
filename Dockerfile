# Usa una imagen oficial de PHP con servidor embebido
FROM php:8.2-cli

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copia todos los archivos del proyecto al contenedor
COPY ./api /app

# Expón el puerto (Railway lo usará)
EXPOSE 8080

# Comando para ejecutar el servidor PHP embebido
# Reemplaza "index.php" por el archivo que actúe como punto de entrada
CMD ["php", "-S", "0.0.0.0:8080"]