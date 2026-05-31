FROM python:3.9-slim-buster
WORKDIR /app
COPY . /app
RUN pip install --no-cache-dir -r requirements.txt
ENV ADMIN_USER_ID="7853478744"
CMD ["python", "bot_updated.py"]
