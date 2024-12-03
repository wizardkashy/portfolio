`timescale 1ns / 1ps
//////////////////////////////////////////////////////////////////////////////////
// Company: 
// Engineer: 
// 
// Create Date: 06/02/2024 09:52:03 PM
// Design Name: 
// Module Name: Controller
// Project Name: 
// Target Devices: 
// Tool Versions: 
// Description: 
// 
// Dependencies: 
// 
// Revision:
// Revision 0.01 - File Created
// Additional Comments:
// 
//////////////////////////////////////////////////////////////////////////////////


module Controller(
    input [6:0] Opcode,
    output [1:0] ALUOp,
    output RegWrite, ALUSrc, MemRead, MemWrite, MemtoReg
    );
    parameter LW = 7'b0000011;
    parameter SW = 7'b0100011;
    parameter I = 7'b0010011;
    parameter NOT_I = 7'b0110011;
    assign MemtoReg = (Opcode == LW)? 1'b1 : 1'b0;
    assign MemWrite = (Opcode == SW)? 1'b1 : 1'b0;
    assign MemRead = (Opcode == LW)? 1'b1 : 1'b0;
    assign ALUSrc = (Opcode == NOT_I)? 1'b0 : 1'b1;
    assign RegWrite = (Opcode == SW)? 1'b0 : 1'b1;
    
    assign ALUOp = (Opcode == NOT_I)? 2'b10 :
                   ((Opcode == I)? 2'b00 : 2'b01);
endmodule
